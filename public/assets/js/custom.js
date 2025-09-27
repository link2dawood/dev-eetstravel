// AOS.init({ once: true });

$(document).ready(function () {
  $(".content-slider").slick({
    dots: true,
    infinite: true,
    autoplay: true,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    fade: true,
    arrows: false,
  });
  $(".chosen-select").chosen({
    no_results_text: "Oops, nothing found!",
  });

  $( "#calendar" ).datepicker();
});
