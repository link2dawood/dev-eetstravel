$(document).ready(function(){
    parseEmails.init();
});

var parseEmails = {
    dom : {
        period : $('#time_period'),
        period_type : $('#period_type'),
        submit : $('#period_submit')
    },
    init : function(){
        parseEmails.bind();
    },
    bind : function(){
        $('#period_submit').click(function(){
            parseEmails.submit();
        });
    },
    submit : function () {
        if (parseEmails.validate()){
            $.ajax({
                type:'GET',
                url:  '/email/parseEmails',
                data: {
                    period : parseEmails.dom.period.val(),
                    period_type : parseEmails.dom.period_type.val(),
                },
                success:function(data){
                    console.log('Alls_good');
                    $('#alert-message').css({'display':'block'});
                    $(parseEmails.dom.submit).attr('disabled', true);
                },
                error: function(data){
                    console.log(data);
                }
            });
        } else {
            console.log('validate problem');
        }
    },
    validate : function() {
        if(parseEmails.dom.period.val() == "") {
            return false;
        }
        return true;
    }
};