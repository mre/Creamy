<form id="post-form" action="editor.php" method="post">
  <div class="editor-head">
    <div id="wmd-button-bar" class="left"></div>
    <input class="button" id="submit-button" type="submit" name="submit" value="Save"></input>
  </div>
  <div class="editor-view">
    <div class="panel left">
      <textarea id="wmd-input" name="post-text"><?=$content?></textarea>
    </div>
    <div class="panel right" id="wmd-preview"></div>
    <div class="clear"></div>
  </div>
</form>
<script type="text/javascript" src="wmd/wmd.js"></script>
