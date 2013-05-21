<? if (isset($error) && (!is_array($error) || count($error) != 0)) { ?>
  <div class="error">
    <?
        if (is_array($error)) {
            foreach ($error as $e) {
                echo $e . '<br/>';
            }
        } else {
            echo $error;
        }
    ?>
  </div>
<? } ?>

