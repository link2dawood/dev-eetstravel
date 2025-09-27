$(document).ready(function () {

  let headerHeight = $('.header').height();
  $('.main .main-content').css('margin-top', headerHeight);

  $('.hero-slider').slick({
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    autoplay: true
  });
  if(window.innerWidth <= 991) {
    $('.collage-slider').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      autoplay: true
    });
  }




  var imageArray = [
    'https://images.unsplash.com/photo-1682687220015-186f63b8850a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1375&q=80',
    'https://images.unsplash.com/photo-1683009680116-b5c04463551d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=870&q=80',
    'https://images.unsplash.com/photo-1549877452-9c387954fbc2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=870&q=80',
    // Add more image URLs as needed
  ];
  var imageDiv = $('#tourCreate');
  var imageIndex = 0;

  function changeImage() {
    var imageUrl = imageArray[imageIndex];
    imageDiv.css('background-image', 'url(' + imageUrl + ')');
  }
  setInterval(function () {
    imageIndex++;
    if (imageIndex >= imageArray.length) {
      imageIndex = 0; // Reset index when it reaches the end of the array
    }
    changeImage();
  }, 4000);


  // form wizard start

  var form = $("#tourCreateForm");

  form.steps({
    headerTag: "h6",
    bodyTag: "formstep",
    transitionEffect: "fade",
    titleTemplate: '<span class="step">#index#</span> #title#',
    labels: {
      next: "Next",
      previous: "Previous",
      finish: "Submit"
    },
    onFinished: function(event, currentIndex) {
      // Handle the form submission
      $("#tourCreateForm").submit();
    }
  });
  $('.actions ul li a').addClass('btn btn-primary');



  $('.slider-s1').slick({
    infinite: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    variableWidth: true
  });

  let hotelSlider = $('.hotels-slider');
  $('.card', hotelSlider).on('click', function(){
    $('.card', hotelSlider).removeClass('active');
    $(this).addClass('active');
    $(this).children().find('input').prop('checked', true);
  });

  // form wizard end



});
