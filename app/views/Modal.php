<?php

/**
 * Modal
 */
class Modal
{
    public function __construct($args)
    {
        $id = $args["id"] ?? "Modal";
        $title = $args["title"] ?? "";
        $body = $args["body"] ?? "";
        $footer = $args["footer"] ?? "";

        echo <<<EOT
<div id="$id" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">$title</h4>
            </div>
            <div id="ModalBody" class="modal-body">$body</div>
            <div class="modal-footer">$footer</div>
        </div>
    </div>
</div>
<div id="ModalBackdrop" class="modal-backdrop fade" style="display:none"></div>
EOT;
    }
}
