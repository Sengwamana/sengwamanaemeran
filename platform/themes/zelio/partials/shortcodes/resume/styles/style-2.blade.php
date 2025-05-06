<section id="resume" class="section-education">
    <div class="container">
        <div @class(['row row-cols-1', 'row-cols-lg-1' => count($resumes) === 1, 'row-cols-lg-2' => count($resumes) > 1])>
            @foreach($resumes as $resume)
                @php
                    $iconKey = "resume_{$loop->iteration}_title_icon";
                    $titleKey = "resume_{$loop->iteration}_title";
                @endphp

                <div class="col pt-3">
                    <div class="rounded-3 border border-1 position-relative h-100 overflow-hidden">
                        <div class="box-linear-animation p-md-6 p-3 ">
                            <div class="d-flex align-items-center">
                                @if ($icon = $shortcode->{$iconKey})
                                    <x-core::icon :name="$icon" />
                                @endif
                                <h2 class="mb-0 ms-2">{!! BaseHelper::clean($shortcode->{$titleKey}) !!}</h2>
                            </div>
                            <div class="d-flex flex-column h-100 position-relative mt-5">
                                <ul class="ps-3">
                                    @foreach($resume as $item)
                                        <li class="position-relative z-1 mb-3">
                                            <div class="d-flex gap-2">
                                                <p class="text-300 text-nowrap fw-regular mb-0">{{ $item['time'] }}</p>
                                                <div>
                                                    @if($item['title'])
                                                        <span class="text-primary-2">{{ $item['title'] }}</span>
                                                    @endif
                                                    @if($item['subtitle'])
                                                        <p class="text-dark">{{ $item['subtitle'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="line-left position-absolute top-0 border-start z-0"></div>
                            </div>
                            <div class="bg-overlay position-absolute bottom-0 start-0 z-1"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
