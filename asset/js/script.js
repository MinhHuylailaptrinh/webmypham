// Khởi tạo Flexslider cho tất cả slider có class 'product-slider'
$(".product-slider").each(function () {
  $(this).flexslider({
    animation: "slide",
    controlNav: "thumbnails",
  });
});

// Khởi tạo Flexslider cho tất cả carousel có class 'product-carousel'
$(".product-carousel").each(function () {
  $(this).flexslider({
    animation: "slide",
    controlNav: "thumbnails",
  });
});
