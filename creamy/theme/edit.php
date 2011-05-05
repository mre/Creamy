<form id="post-form" action="editor.php" method="post">
  <div class="wmd-panel">
    <div id="wmd-button-bar" ></div>
    <textarea id="wmd-input" name="post-text"><?=$content?></textarea>

    <div id="wmd-preview"></div>
  </div>

  <div class="form-submit">
    <input id="submit-button" type="submit" name="submit" value="Save">
</form>
<script type="text/javascript" src="wmd/wmd.js"></script>
