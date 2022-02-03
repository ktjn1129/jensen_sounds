<footer id="footer">
  <ul class="footer_menu">
    <li><a href="">About JENSEN SOUNDS</a></li>
    <li><a href="">Contact</a></li>
    <li><a href="">Privacy Policy</a></li>
    <li><a href="">Cookies Policy</a></li>
  </ul>
  <ul class="sns_icon">
    <li><a href=""><i class="fab fa-facebook-f"></i></a></li>
    <li><a href=""><i class="fab fa-instagram"></i></a></li>
    <li><a href=""><i class="fab fa-twitter"></i></a></li>
  </ul>
  <p>&copy; 2021-2022 JENSEN SOUNDS All rights reserved.</p>
</footer>

<script>
  $(function(){

    $('.pickup:not(:eq(-1)):not(:eq(-1)):not(:eq(-1)):not(:eq(-1)):not(:eq(-1))').mouseover(function(){
      $(this).animate({'marginTop':'-120px'}, 400);
    }).mouseout(function(){
      $(this).animate({'marginTop':'0px'});
    });

    var $footer = $('#footer');
    if(window.innerHeight > $footer.offset().top + $footer.outerHeight()){
      $footer.attr({'style': 'position:fixed; top:' + (window.innerHeight - $footer.outerHeight()) + 'px; width:100%'});
    }

    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
      $jsShowMsg.slideToggle('slow');
      setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
    }

    var $like,
        likeProductId;
    $like = $('.js-click-like') || null;
    likeProductId = $like.data('productid') || null;

    if(likeProductId !== undefined && likeProductId !== null){
      $like.on('click',function(){
        var $this = $(this);
        $.ajax({
          type: "post",
          url: "ajaxLike.php",
          data: { productId : likeProductId }
        }).done(function(data){
          console.log('Ajax Succeeded');
          $this.toggleClass('active');
        }).fail(function(msg){
          console.log('Ajax Error');
        });
      });
    }

  });
</script>

</body>
</html>
