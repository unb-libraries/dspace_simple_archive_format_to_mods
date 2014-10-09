# DSpace Simple Archive Format to MODS converter
Converts a tree of DSpace generated Simple Archive Format export to MODS XML, suitable for an islandora Batch import.

Because the Dublin Core dumped out by DSPace is meta-dublin core, this script requires web access to the OAI interface of the DSpace repository itself. This is extremely inconvenient and sloppy.

## To Note:
 * Because Islandora's batch only allows one datastream per XML file, this script only identifies the first file in the CONTENTS file and uses that.
