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
$preferred_datastream_bundle = 'bundle:ORIGINAL';
$xslt_path='xslt/dc_to_thesis_mods.xsl';

$counter = 0;
$files_not_imported=array();

foreach (scandir($path_to_parse, SCANDIR_SORT_ASCENDING) as $cur_dspace_bundle) {
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
    $previously_added_file = False;
    $cur_file_signature = False;
    foreach (explode("\n", $contents_file) as $cur_bundle_file) {
      $bundle_file_data = explode("\t",$cur_bundle_file);
      $cur_file_signature = "$path_to_parse/$cur_dspace_bundle/{$bundle_file_data[0]}/{$bundle_file_data[1]}";
      if (substr($bundle_file_data[0], -4)  == '.pdf' && $bundle_file_data[1] == $preferred_datastream_bundle) {
        // Add this file to the current
        $cur_bundle_pdf = "$path_to_parse/$cur_dspace_bundle/{$bundle_file_data[0]}";
        if ($previously_added_file) {
          $files_not_imported[] = $previously_added_file;
          print "Did not import $previously_added_file\n";
        }
        $previously_added_file = $cur_file_signature;
        print "$cur_bundle_pdf\n";
      } else {
        $files_not_imported[] = $cur_file_signature;
        print "Did not import $cur_file_signature\n";
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
    $transformXSL->load($xslt_path);

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

file_put_contents('files_not_imported.txt', print_r($files_not_imported, true));
