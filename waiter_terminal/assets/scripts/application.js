let application_classes = (function() {

    function application_classes(){
        this.db.accounts = application_db_accounts;
        this.db.table_sections = application_db_table_sections;
        this.db.info = application_db_info;
        this.application = application_application;
        this.notifications = application_notifications;
    }

    application_classes.prototype.db = {
        accounts: null,
        table_sections: null,
        info: null
    }

    application_classes.prototype.application = null;

    application_classes.prototype.notifications = null;


    return application_classes;
})();

let application = new application_classes();