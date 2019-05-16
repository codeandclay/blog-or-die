<?php

defined( 'ABSPATH' ) || die();

class BlogOrDieAdminNoticeBase {
    protected $message;

    public function __construct( $message ) {
        $this->message = $message;
    }

    public function display(){
        $type = $this->type();
        ?>
        <div class="notice notice-<?php echo($type) ?> is-dismissible">
            <p><strong>Blog or Die: </strong><?php echo($this->message); ?></p>
        </div>
        <?php
    }

}

class BlogOrDieAdminNoticeSuccess extends BlogOrDieAdminNoticeBase {
    protected function type() {
        return 'success';
    }
}

class BlogOrDieAdminNoticeInfo extends BlogOrDieAdminNoticeBase {
    protected function type() {
        return 'info';
    }
}

class BlogOrDieAdminNoticeWarning extends BlogOrDieAdminNoticeBase {
    protected function type() {
        return 'warning';
    }
}

class BlogOrDieAdminNoticeError extends BlogOrDieAdminNoticeBase {
    protected function type() {
        return 'error';
    }
}

?>
