<?php

//============================================================================================

// SETTINGS:

// Absolute path to the statistics files (NO trailing slash!)
$new_notes_stats_path = '/path/for/messages/stats';

// Absolute path to the directory which contains notes and note counters (NO trailing slash!)
$absolute_note_path = '/path/for/messages';

//============================================================================================

// Resets
$count = '0';
$new_notes_1d = '0';
$new_notes_7d = '0';
$new_notes_30d = '0';
$page_hits = '0';

// Count the amount of all individual notes, substract all . & .. folder entries (18 in total) and round the result
$count = $count + count(glob("$absolute_note_path/15m/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/30m/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/45m/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/1h/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/6h/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/12h/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/1/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/3/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/7/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/30/" . "*.counter"));
$count = $count + count(glob("$absolute_note_path/60/" . "*.counter"));
$count = round($count);

// Count the newly created messages
$new_notes_1d = iterator_count(new FilesystemIterator("$new_notes_stats_path/1/", FilesystemIterator::SKIP_DOTS));
$new_notes_7d = iterator_count(new FilesystemIterator("$new_notes_stats_path/7/", FilesystemIterator::SKIP_DOTS));
$new_notes_30d = iterator_count(new FilesystemIterator("$new_notes_stats_path/30/", FilesystemIterator::SKIP_DOTS));

// Count page hits
$page_hits = iterator_count(new FilesystemIterator("$new_notes_stats_path/hits/", FilesystemIterator::SKIP_DOTS));

// Write stats to files
// 1
$fp = fopen("$new_notes_stats_path/1.txt", "w");
fwrite($fp , $new_notes_1d);
fclose($fp);

//echo "1: $new_notes_1d<br />";

// 7
$fp = fopen("$new_notes_stats_path/7.txt", "w");
fwrite($fp , $new_notes_7d);
fclose($fp);

//echo "7: $new_notes_7d<br />";

// 30
$fp = fopen("$new_notes_stats_path/30.txt", "w");
fwrite($fp , $new_notes_30d);
fclose($fp);

//echo "30: $new_notes_30d<br />";

// Waiting to be read
$fp = fopen("$new_notes_stats_path/waiting.txt", "w");
fwrite($fp , $count);
fclose($fp);

//echo "waiting: $count<br />";

// Page hits
$fp = fopen("$new_notes_stats_path/hits.txt", "w");
fwrite($fp , $page_hits);
fclose($fp);

//echo "hits: $page_hits<br />";

// Print status
echo "0";

?>
