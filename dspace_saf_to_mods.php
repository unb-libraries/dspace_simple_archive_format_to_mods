<?php

/**
 * @file
 * Convert Dspace Batch Simple Archive Format to MODS
 */

$dspace_url = 'http://dspace.hil.unb.ca:8180';
$dspace_root_oai_namespace = 'oai:dspace.hil.unb.ca';
$path_to_parse = '/Volumes/Macintosh HD 2/dspace/collection_out/senior_reports';
$output_path = '/Volumes/Macintosh HD 2/dspace/output';
$sleep_each_item = 2;
$xslt_path='xslt/dc_to_thesis_mods.xsl';

$counter = 0;
foreach (scandir($path_to_parse, SCANDIR_SORT_ASCENDING) as $cur_dspace_bundle) {
  if ($counter >= $limit) {
    die("$limit Limit Reached");
  }
  sleep($sleep_each_item);
  if ($cur_dspace_bundle != '..' &&  $cur_dspace_bundle != '.') {

  $cur_bundle_pdf = FALSE;
  $cur_dc_file = FALSE;
  $cur_handle = FALSE;

  // Get Dublin Core
  $cur_dc_file = file_get_contents("$path_to_parse/$cur_dspace_bundle/dublin_core.xml");
  if ($cur_dc_file) {
    $dc_xml = simplexml_load_string($cur_dc_file);
    $dc_title = (string) $dc_xml->xpath("dcvalue[@element='title']")[0];
    $dc_title = str_replace('/',' ', $dc_title);
    print "$dc_title\n";
  }

  // Get Handle
  $cur_handle = trim(file_get_contents("$path_to_parse/$cur_dspace_bundle/handle"));

  // Check for Attached Files
  $contents_file = file_get_contents("$path_to_parse/$cur_dspace_bundle/contents");
  if ($contents_file) {
    foreach (explode("\n", $contents_file) as $cur_bundle_file) {
        $bundle_file_data = explode("\t",$cur_bundle_file);
        if (substr($bundle_file_data[0], -4)  == '.pdf' && $bundle_file_data[1] == 'bundle:ORIGINAL') {
          // Add this file to the current
          $cur_bundle_pdf = "$path_to_parse/$cur_dspace_bundle/{$bundle_file_data[0]}";
          print "$cur_bundle_pdf\n";
        }
      }
    }

  // Write out bundle
  if ($cur_dc_file && $cur_handle) {
    // Get DC portion of OAI Export
    $oai_url = "$dspace_url/oai/request?verb=GetRecord&identifier=$dspace_root_oai_namespace:$cur_handle&metadataPrefix=oai_dc";
    $oai_content = file_get_contents($oai_url);

    // Remove OAI wrapper cruft
    $oai_content = preg_replace('/.*<oai_dc:dc xml.*?>/', '', $oai_content);
    $oai_content = preg_replace('/<\/oai_dc:dc>.*/', '', $oai_content);
    $oai_content = '<oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">' .
      "\n".
      $oai_content .
      "\n" .
      '</oai_dc:dc>';

    // Remove handle reference, this isn't valid anymore
    $oai_content = preg_replace('|<dc:identifier>http:\/\/hdl.handle.net/|', '<dc:identifier>', $oai_content);

    print $oai_content;

    $dc_xml = new DOMDocument();
    $dc_xml->loadXML($oai_content);

    // Use new dc:x in stylesheet
    $transformXSL = new DOMDocument();
    $transformXSL->load('$xslt_path);

    $processor = new XSLTProcessor();
    $processor->importStylesheet($transformXSL);

    $mods_xml = new DOMDocument();
    $mods_xml->loadXML($processor->transformToXML($dc_xml));

    file_put_contents("$output_path/$counter.xml", $mods_xml->saveXML());

    if ($cur_bundle_pdf) {
      copy($cur_bundle_pdf, "$output_path/$counter.pdf");
    }
  }
  $counter++;
  }
}
