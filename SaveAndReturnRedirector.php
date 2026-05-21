<?php

namespace UniversityOfWashington\SaveAndReturnRedirector;

use ExternalModules\AbstractExternalModule;

class SaveAndReturnRedirector extends AbstractExternalModule {

    function redcap_save_record(
        $project_id,
        $record,
        $instrument,
        $event_id,
        $group_id,
        $survey_hash,
        $response_id,
        $repeat_instance
    )
    {
        // Intentionally left blank.
        // Redirect handling is now fully client-side to avoid
        // REDCap EM hook lifecycle termination warnings.
    }

    function redcap_survey_complete(
        $project_id,
        $record,
        $instrument,
        $event_id,
        $group_id,
        $survey_hash,
        $response_id,
        $repeat_instance
    )
    {
        $redirectUrl = $this->getProjectSetting('global_redirector_button_url');

        ?>
        <script type="text/javascript">
        $(document).ready(function() {

            const redirectUrl = <?= json_encode($redirectUrl) ?>;

            if (redirectUrl) {
                setTimeout(function() {
                    window.location.href = redirectUrl;
                }, 500);
            }

        });
        </script>
        <?php
    }

    function redcap_survey_page(
        $project_id,
        $record,
        $instrument,
        $event_id,
        $group_id,
        $survey_hash,
        $response_id,
        $repeat_instance
    )
    {
        $addBtn = $this->getProjectSetting('global_show_redirector_button');

        if (!$addBtn) {
            return;
        }

        $addBtnText = $this->getProjectSetting('global_redirector_button_text');
        $redirectUrl = $this->getProjectSetting('global_redirector_button_url');

        ?>
        <script type="text/javascript">
        $(document).ready(function() {

            const returnBtn = $("[name='submit-btn-savereturnlater']");
            const buttonText = <?= json_encode($addBtnText) ?>;
            const redirectUrl = <?= json_encode($redirectUrl) ?>;

            if (returnBtn.length) {

                if (buttonText) {
                    returnBtn.val(buttonText);
                    returnBtn.text(buttonText);
                }

                returnBtn.off('click.redirector');

                returnBtn.on('click.redirector', function(e) {

                    // Allow REDCap's normal save process
                    // to proceed before redirecting.
                    setTimeout(function() {

                        if (redirectUrl) {
                            window.location.href = redirectUrl;
                        }

                    }, 500);

                });
            }

        });
        </script>
        <?php
    }
}