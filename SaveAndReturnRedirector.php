<?php
namespace UniversityOfWashington\SaveAndReturnRedirector;

use ExternalModules\AbstractExternalModule;

class SaveAndReturnRedirector extends AbstractExternalModule {

    public function __construct(){
        parent::__construct();
    }

    function redcap_save_record($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
        // REDCap::logEvent(__FUNCTION__, "$record - $instrument - $event_id");

        // only fire on surveys
        if (empty($survey_hash)) return;

        if (isset($_GET['__return'])) {
            // This is a save and return
            $this->redirectToPortal();
        }
    }

function redirectToPortal() {
    if (!$this->getProjectSetting('global_show_redirector_button')) return;

    $url = $this->getProjectSetting('global_redirector_button_url');
    if (empty($url)) return; // nothing configured, don't redirect to nowhere

    if (headers_sent()) {
        echo "<script type='text/javascript'>window.location.href=" . json_encode($url) . ";</script>";
    } else {
        header('Location: ' . $url, true, 302);
    }
    $this->exitAfterHook();
}

    function redcap_survey_page($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
        if (isset($_GET['sq'])) {
            // on the queue page
            $this->redirectToPortal();
            return;
        }

        $showButton = $this->getProjectSetting('global_show_redirector_button');
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
            const showButton = <?= json_encode((bool)$showButton) ?>;
            if (!showButton) return;

            const returnBtn = $("[name='submit-btn-savereturnlater']");
            const buttonText = <?= json_encode($this->getProjectSetting('global_redirector_button_text')) ?>;

            if (returnBtn.length && buttonText) {
                returnBtn.val(buttonText);
                returnBtn.text(buttonText);
            }

            if ($("button[name='submit-btn-saverecord']").is(':visible')
                && $("button[name='submit-btn-saverecord']").text()=='Submit') {
                $("button[name='submit-btn-savereturnlater']").hide();
            }
        });
        </script>
        <?php	
    }
}