
# bookmark2wiki

A DokuWiki plugin to save bookmarks from your browser to configurable wiki pages using a bookmarklet. Forked from `post2wiki.php` by riny [at] bk [dot] ru.

## Overview

This plugin allows users to bookmark webpages by sending the URL, title, and optionally highlighted text to a specified DokuWiki page. The bookmark can be added either after the first heading (prepend) or at the end of the page (append), as configured in DokuWiki's Admin > Configuration. The plugin uses DokuWiki's standard functions (`rawWiki` and `saveWikiText`) to append content without direct file system access, ensuring compatibility with DokuWiki's security model.

- **Version**: 1.0
- **License**: GPL 2 (http://www.gnu.org/licenses/gpl.html)
- **Author**: edwardcodelab (https://github.com/edwardcodelab)
- **Todo**: Enhance input validation for security (e.g., stricter sanitization of URL, title, and text).

## Features

- Save bookmarks to user-defined wiki pages via a dropdown selection in a popup.
- Configurable insertion point: after the first heading (prepend) or at the end of the page (append).
- Supports highlighted text from the webpage, appended as part of the bookmark entry.
- Uses DokuWiki's permission system to ensure only authorized users can edit pages.
- Simple bookmarklet for easy browser integration.

## Installation

1. Clone or download the repository to your DokuWiki plugin directory:
   ```
   /lib/plugins/bookmark2wiki/
   ```
   Ensure the directory contains:
   - `action.php`
   - `conf/default.php`
   - `conf/metadata.php`
   - `plugin.info.txt`

2. Clear DokuWiki's cache if needed

3. Configure the plugin:
   - In **Admin > Configuration**, find the `bookmark2wiki` settings.
   - Set `pages`: Enter page IDs (e.g., `new_bookmarks`, `namespace:bookmarks`) one per line.
   - Set `position`: Choose "After Heading (prepend)" or "End of Page (append)" from the dropdown.

4. Create the bookmarklet:
   - Copy the JavaScript below, replacing `https://myserver/doku.php` with your DokuWiki base URL.
   - Drag the bookmarklet to your browser's toolbar or add it manually.

   **Bookmarklet**:
   ```javascript
   javascript:(function(){Q=document.selection?document.selection.createRange().text:document.getSelection();void(window.open('https://myserver/doku.php?do=bookmark2wiki&te='+encodeURIComponent(Q)+'&ur='+encodeURIComponent(location.href)+'&ti='+encodeURIComponent(document.title),'dokuwikiadd','scrollbars=yes,resizable=yes,toolbar=no,width=400,height=200,left=200,top=200,status=yes'));})();
   ```

## Usage

1. Navigate to a webpage you want to bookmark.
2. Optionally, highlight text on the page to include in the bookmark.
3. Click the bookmarklet in your browser toolbar.
4. In the popup, select a target page from the dropdown (configured in Admin > Configuration).
5. Click "Save" to add the bookmark.
   - If set to "After Heading (prepend)", the bookmark appears after the first heading (e.g., `====== New Articles ======`) in the page.
   - If set to "End of Page (append)", the bookmark is added at the page's end.
6. The popup confirms the save and closes automatically after 3 seconds.

**Bookmark Format**:
```
  * [[URL|Title]] \\ HighlightedText -- YYYY:MM:DD:HH:MM:SS
```

## Notes

- **Security**: The plugin includes basic input sanitization (e.g., URL validation, HTML escaping). Further security enhancements are planned (e.g., rate limiting, stricter input checks).
- **Permissions**: Users must be logged into DokuWiki with edit permissions for the target page. The plugin checks this using `auth_quickaclcheck`.
- **Configuration**: Ensure pages listed in the `pages` setting exist or are editable, and verify the `position` setting in Admin > Configuration.
- **Debugging**: If issues occur, check `conf/local.php` for correct `position` value (`'top'` or `'bottom'`) and clear cache after changes.

## Troubleshooting

- **Bookmarklet fails**: Ensure the URL in the bookmarklet matches your DokuWiki installation (e.g., `https://myserver/doku.php`).
- **Wrong insertion point**: Verify the `position` setting in Admin > Configuration is set to "After Heading (prepend)" for top insertion.
- **No pages in dropdown**: Check that `pages` in Admin > Configuration contains valid page IDs (e.g., `new_bookmarks`).
- **Contact**: Report issues at https://github.com/edwardcodelab/bookmark2wiki.

