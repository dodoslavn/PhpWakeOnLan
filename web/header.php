<html>
  <head>
    <title>PhpWakeOnLan</title>
    <link rel="stylesheet" type="text/css" href="/features/main_page.css">
    <script>
        function autoSubmitForm(checkbox) 
            {
            var form = checkbox.closest('form');
            if (form) { form.submit(); }
            }
    </script>
  </head>
  <body>
  <div id="header">
    <div id="title">PhpWakeOnLan</div>
    <div id="links">
      <a href="/arp/">ARP</a>
      <a href="/settings/"><? global $lang; echo $lang['header_settings']; ?></a>
      <a href="/logout/"><? global $lang; echo $lang['header_logout']; ?></a>
    </div>
  </div>
<div id="content">
