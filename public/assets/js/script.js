$(document).ready(function() {
    $('#app').hide();
    $('#botLogoOnMobile').hide();

    var counter = 0;
    var c = 0;
    var i = setInterval(function(){
        $(".loading-page .counter h1").html(c + "%");
        $(".loading-page .counter hr").css("width", c + "%");
      counter++;
      c++;
        
      if(counter == 102) {
          clearInterval(i);
          $('.loading-page').hide();
          $('#app').fadeIn();
          $('#botLogoOnMobile').fadeIn();
      }
    }, 50);
});

$('#launchBot').on('submit', function(event) {
    event.preventDefault();
    const data = {
        email: $('#email').val(),
        name: $('#name').val(),
        password: $('#password').val()
    };

    // Add animation
    $('#robotLoading > img').addClass('botThinkingAnimation');
    $('#robotStatus').html("Je m'occupe de tout...");

    if(data.email.value !== null && data.name.value !== null && data.password.value !== null)
    {
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: {
                '_token': $('input[name="_token"]').val(),
                'email': data.email,
                'name': data.name,
                'password': data.password
            },
            success: function(data) {
                $('#robotLoading > img').removeClass('botThinkingAnimation');
                $('#robotLoading').hide();
                $('#robotWorking').show();
                // Get total of records
                const totalOfRecords = data.totalResidenceFound;
                $('#afterWorkingEnd').html("Sur "+totalOfRecords+" résidences trouvées, j'ai été capable de récupérer "+ data.totalReceiverMails +" emails puis générer et envoyer "+data.totalMessageSends+" mails. <br> <p style='font-size:10px; color:grey'>Pour éviter les SPAMs, le bot ne renvoit pas des mails aux mêmes destinataires.</p>")
            },
            error: function(error) {
                $('#robotStatus').html("");
                $('#robotStatus').html("Oups j'ai rencontré une erreur...");

                setTimeout(function(){
                    $('#robotStatus').html("Recommençons une nouvelle fois...");
                    location.reload();
                }, 5000);
                console.log(error);
            }
        })   
    }
});

$('#botLogoOnMobile').on('click', function() {
    $('#chatWithRobot').show();
    $('#chatWithRobot').removeClass("h-screen");
    $('html, body').css({
        overflow: 'hidden',
        height: '100%'
    });
    $('#overlay').css("background", "#FFFFF");
    $('#overlay').fadeIn();
});
$('#overlay').on('click', function() {
    $('#chatWithRobot').hide();
    $('#overlay').hide();
    $('html, body').css({
        overflow: 'auto',
    });
});

// Discussion with bot content switch
$('#startBot').on('click', function() {
    if($('#email').val() !== null) 
    {
        $('#stepOne').hide()
        $('#stepTwo').show()
    }
});
$('#acceptConditions').on('click', function() {
    $('#stepTwo').hide()
    $('#stepThree').show()
});
$('#refuseConditions').on('click', function() {
    $('#stepTwo').hide()
    $('#stepOne').show()
});
$('#returnToStepTwo').on('click', function() {
    $('#stepTwo').show()
    $('#stepThree').hide()
});
$('#returnToStepThree').on('click', function() {
    $('#stepThree').show()
    $('#stepFourth').hide()
});
$('#continueToStepFourth').on('click', function() {
    $('#stepThree').hide()
    $('#stepFourth').show()
});
$('#endStep').on('click', function() {
    $('#stepFourth').hide()
    $('#robotLoading').show()
    // $('#robotLoading > img').addClass('botThinkingAnimation');

    // setTimeout(() => {
    //      $('#robotLoading').hide();
    //      $('#robotWorking').show()
    // }, 5000)
});