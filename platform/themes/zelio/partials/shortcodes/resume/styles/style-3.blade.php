<div id="resume" class="education pt-70">
    <div class="row">
        @foreach($resumes as $resume)
            @php
                $titleKey = "resume_{$loop->iteration}_title";
            @endphp
            <div class="col-6">
                <h3 @class(['d-none d-md-block' => $loop->last])>{!! BaseHelper::clean($shortcode->{$titleKey}) !!}</h3>
            </div>
        @endforeach
    </div>
    <div class="row pt-4">
        @foreach($resumes as $resume)
            @php
                $titleKey = "resume_{$loop->iteration}_title";
                $displayTypeKey = "resume_{$loop->iteration}_display_type";
            @endphp

            <div @class(['col-md-6 align-self-stretch', 'h-100' => $loop->first, 'mt-md-0 mt-5' => ! $loop->first])>
                @if($shortcode->{$displayTypeKey} == 'default')
                    <div class="card-services rounded-4 border border-secondary-3 bg-white p-lg-5 p-md-4 p-3 h-100">
                        @foreach($resume as $item)
                            @if($item['image'])
                                <div class="icon rounded-circle overflow-hidden d-inline-block">
                                    {{ RvMedia::image($item['image'], $item['title'], 'thumb') }}
                                </div>
                            @endif
                            <p class="text-dark fs-5 mt-1 mb-2">{{ $item['title'] }}</p>
                            <ul @class(['d-flex gap-4 ps-3', 'border-bottom pb-2 mb-4' => ! $loop->last, 'mb-0' => $loop->last])>
                                <li>
                                    <p>{{ $item['time'] }}</p>
                                </li>
                                <li>
                                    <p class="text-primary-3">{{ $item['subtitle'] }}</p>
                                </li>
                            </ul>
                        @endforeach
                    </div>
                @else
                    <h3 class="d-block d-md-none">{{ $shortcode->{$titleKey} }}</h3>
                    <div class="card-award rounded-4 border border-secondary-3 bg-white p-lg-5 p-md-4 p-3 align-self-stretch h-100 overflow-hidden">
                        <div class="position-relative h-100 align-self-stretch align-items-center">
                            <ul class="list-style-1 d-flex ps-3 flex-column mb-0">
                                @foreach($resume as $item)
                                    <li class="position-relative z-1">
                                        <p class="fs-5 text-dark mb-2">{{ $item['title'] }}</p>
                                        <ul class="list-style-2 d-flex gap-4 ps-3">
                                            @if($item['subtitle'])
                                                <li>
                                                    <p class="text-primary-3 mb-0">{{ $item['subtitle'] }}</p>
                                                </li>
                                            @endif
                                            @if($item['time'])
                                                <li>
                                                    <p class="mb-2">{{ $item['time'] }}</p>
                                                </li>
                                            @endif
                                        </ul>
                                        @if($item['description'])
                                            <p class="mb-4">{{ $item['description'] }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <div class="line-left position-absolute top-0 border-start h-md-100 z-0"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
