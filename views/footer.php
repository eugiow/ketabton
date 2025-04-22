<!-- footer -->

<footer >
    <section class="bg-white mb-5 mx-3 p-3 rounded-lg back-footer">
        <div class="flex flex-row-reverse  items-center justify-between ">
            <div class=" flex items-center justify-center sm:justify-end ">
                <a href=""><img class="w-24 " src="assets/images/icon/logotext.webp" alt=""></a>
            </div>

            <div class="flex   us-img justify-center sm:justify-start ">
                <div >
                    <a class="tele" href=""><i class="bi bi-telegram"></i></a>
                </div>
                <div>
                    <a class="insta" href=""><i class="bi bi-threads-fill"></i></a>
                </div>
            </div>
        </div>
    </section>
</footer>





    <script>
     
  var splide = new Splide( '#slide-head', 
  {
  focus      : 'center',
  type      : 'loop',
  height    : '20rem',
  autoplay: 'true',
  arrows: false,
  fixedWidth : '10rem',
  fixedHeight: '6rem',
  gap        : '1rem',
  trimSpace: false,

  
} );
splide.mount();


    
        </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Splide('#splide', {
            type: 'loop', 
            perPage: 5,   
            perMove: 1,   
            gap: '1rem',  
            autoplay: true, 
            interval: 3000, 
            breakpoints: {
                1024: { perPage: 4 },
                768: { perPage: 3 },
                480: { perPage: 2 },
            },
        }).mount();
    });
</script>
