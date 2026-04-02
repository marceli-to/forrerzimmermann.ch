@props([
  'info' => null,
])

<div {{ $attributes->merge(['class' => 'swiper']) }} data-slideshow>
  
  <div class="swiper-wrapper relative">
    
    <div class="swiper-slide w-col-3-offset pl-40 pr-10 justify-center items-center bg-white !hidden lg:!flex">
      @if($info)
        {{ $info }}
      @endif
    </div>
    
    {{ $slot }}

  </div>

  <button class="swiper-btn-prev cursor-pointer absolute left-0 top-0 z-50 w-20 lg:w-120 xl:w-200 h-(--slideshow-item-height) md:h-(--slideshow-item-height-md) xl:h-(--slideshow-item-height-xl)"></button>
  <button class="swiper-btn-next cursor-pointer absolute right-0 top-0 z-50 w-20 lg:w-120 xl:w-200 h-(--slideshow-item-height) md:h-(--slideshow-item-height-md) xl:h-(--slideshow-item-height-xl)"></button>

</div>