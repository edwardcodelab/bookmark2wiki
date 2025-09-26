<?php
/**
 * DokuWiki Plugin bookmark2wiki (Action Component)
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author dodotori <dodotori@localhost>
 * forked from post2wiki.php by riny [at] bk [dot] ru
 * To bookmark webpage using bookmarklet
 * The app will add the url, title and hightlighed text you want to the end of the content of the targeted namespace. It does not directly read/write the dokuwiki page folder.
 * Version 1.0
 * @license GPL 2[](http://www.gnu.org/licenses/gpl.html)
 * @author dodotori https://github.com/edwardcodelab
 */
// TYPICAL USAGE :
// Create bookmarklet as shown in the bookmarklet part below:
// Change the window.open statement to reflect the location of the bookmark2wiki.php script.
// Drag bookmarklet to your toolbar.
// BOOKMARKLET :
// javascript:Q=document.selection?document.selection.createRange().text:document.getSelection(); void(window.open('https://myserver/doku.php?do=bookmark2wiki&te='+escape(Q)+'&ur='+ escape(location.href)+'&ti='+escape(document.title),'dokuwikiadd','scrollbars=yes,resizable=yes,toolbars=yes,width=400,height=200,left=200,top=200,status=yes'));

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\EventHandler;
use dokuwiki\Extension\Event;

class action_plugin_bookmark2wiki extends ActionPlugin
{
    /** @inheritDoc */
    public function register(EventHandler $controller)
    {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'allowMyAction');
        $controller->register_hook('TPL_ACT_UNKNOWN', 'BEFORE', $this, 'performMyAction');
    }

    /**
     * Event handler for ACTION_ACT_PREPROCESS
     *
     * @param Event $event event object by reference
     * @param mixed $param optional parameter passed when event was registered
     * @return void
     */
    public function allowMyAction(Event $event, $param) {
        if($event->data != 'bookmark2wiki') return;
        $event->preventDefault();
    }

    /**
     * Event handler for TPL_ACT_UNKNOWN
     *
     * @param Event $event event object by reference
     * @param mixed $param optional parameter passed when event was registered
     * @return void
     */
    public function performMyAction(Event $event, $param) {
       if($event->data != 'bookmark2wiki') return;
       $event->preventDefault();

       // Get params (support both GET from bookmarklet and POST from form)
       $te = $_REQUEST['te'] ?? '';
       $ur = $_REQUEST['ur'] ?? '';
       $ti = $_REQUEST['ti'] ?? '';

       // Decode unicode (from original)
       $string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $ti);
       $ti = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
       $string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $te);
       $te = html_entity_decode($string, ENT_COMPAT, 'UTF-8');

       if (!$ur || !$ti) {
           echo 'Error: Missing URL or title.';
           return;
       }

       // Basic URL validation
       if (!filter_var($ur, FILTER_VALIDATE_URL)) {
           echo 'Error: Invalid URL.';
           return;
       }

       $timestamp = date("Y:m:d:H:i:s");
       $new_entry = "  * [[" . $ur . "|" . $ti . "]] \\\\ " . $te . " -- " . $timestamp;

       if (isset($_POST['save'])) {
           // Handle form submit: directly save using DokuWiki functions
           $namespace = cleanID($_POST['page']);
           if (!$namespace) {
               echo 'Error: No page selected.';
               return;
           }

           // Check edit permission
           if (auth_quickaclcheck($namespace) < AUTH_EDIT) {
               echo 'Error: No permission to edit the selected page.';
               return;
           }

           // Get raw position config and map to internal value
           $raw_position = $this->getConf('position');
           $position_map = [
               'After Heading (prepend)' => 'top',
               'End of Page (append)' => 'bottom'
           ];
           $position = isset($position_map[$raw_position]) ? $position_map[$raw_position] : $raw_position;
           // Fallback to 'bottom' if config is invalid
           if (!in_array($position, ['top', 'bottom'])) {
               $position = 'bottom';
           }

           // Read current page content
           $text = rawWiki($namespace);
           $text = str_replace("\r", "", $text);  // Normalize line endings to \n

           if (trim($text) === '') {
               $text = "====== New Bookmarks ====== \n";
           }

           $lines = explode("\n", $text);

           if ($position == 'top') {
               // Find the position after the first heading (match after trim)
               $insert_pos = 0;
               $found_heading = false;
               for ($i = 0; $i < count($lines); $i++) {
                   $trimmed = trim($lines[$i]);
                   if (preg_match('/^=+.*=+$/', $trimmed)) {
                       $insert_pos = $i + 1;
                       $found_heading = true;
                       break;
                   }
               }

               // Skip empty lines after the heading
               while ($insert_pos < count($lines) && trim($lines[$insert_pos]) === '') {
                   $insert_pos++;
               }

               // Insert the new entry
               array_splice($lines, $insert_pos, 0, $new_entry);
           } else {
               // Append to end, but add a newline if not empty
               if (!empty(trim($lines[count($lines) - 1]))) {
                   $lines[] = '';
               }
               $lines[] = $new_entry;
           }

           $newtext = implode("\n", $lines);

           // Save the updated content
           saveWikiText($namespace, $newtext, 'Added bookmark: ' . $ti, false);  // false = not minor edit

           echo 'Bookmark saved to ' . hsc($namespace) . '! <script>setTimeout(function(){window.close();}, 3000);</script>';
       } else {
           // Display form for page selection
           $configured_pages = explode("\n", trim($this->getConf('pages')));
           if (empty($configured_pages)) {
               echo 'Error: No pages configured in plugin settings.';
               return;
           }

           echo '<form method="post" action="">';
           echo '<label for="page">Save to page:</label><br>';
           echo '<select name="page" id="page">';
           foreach ($configured_pages as $p) {
               $p = trim($p);
               if ($p) {
                   echo '<option value="' . hsc($p) . '">' . hsc($p) . '</option>';
               }
           }
           echo '</select><br>';
           echo '<input type="hidden" name="te" value="' . hsc($te) . '">';
           echo '<input type="hidden" name="ur" value="' . hsc($ur) . '">';
           echo '<input type="hidden" name="ti" value="' . hsc($ti) . '">';
           echo '<input type="submit" name="save" value="Save">';
           echo '</form>';
       }
    }
}