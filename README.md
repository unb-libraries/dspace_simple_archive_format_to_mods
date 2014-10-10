# DSpace Simple Archive Format to MODS converter
Converts a tree of DSpace generated Simple Archive Format export to MODS XML, suitable for an islandora Batch import.

Because the Dublin Core dumped out by DSPace is meta-dublin core, this script requires web access to the OAI interface of the DSpace repository itself. This is extremely inconvenient and sloppy.

## To Note:
As Islandora batch only allows one datastream per XML file, this script only sets up the import of a single file in the CONTENTS file. The default file attached to records is the last one in the $preferred_datastream_bundle, defaulting to BUNDLE:Original.

The script will, however save any datastreams not imported in a log and write them to _files_not_imported.txt_ at the end of the import. This will allow you to audit the conversion and manually import any files that may be important.

A quick way to see potential high-priority items missed by the 1-file-limit import would be:

```cat files_not_imported.txt | grep -v pdf.txt | grep -v None```
