<div id="services" class="my-services pt-70">
    @if($shortcode->title)
        <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
    @endif
    @foreach($services as $service)
        <div @class(['card-services mb-3', 'pt-4' => $loop->first])>
            <div class="card__inner rounded-4 border border-secondary-3 bg-white p-lg-4 p-md-4 p-3 d-flex">
                <div class="d-block">
                    <div class="card__icon icon-shape icon-lg rounded-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <g clip-path="url(#clip0_184_1754)">
                                <path
                                    class="fill-primary-3"
                                    d="M21.1875 7.03125V4.21875H16.8421C15.3434 4.21875 13.9009 4.61381 12.6297 5.36667C12.1617 2.33194 9.51408 0 6.32812 0H5.625V2.8125H2.8125V5.625H0V22.5938H7.15191C9.12042 22.5938 10.1521 23.9276 10.2556 24H13.7444C13.8499 23.9261 14.8715 22.5938 16.8481 22.5938H24V7.03125H21.1875ZM16.8421 5.625H19.7812V18.375H16.8421C15.3722 18.375 13.9563 18.7552 12.7031 19.48V7.00472C12.8124 6.9488 14.3378 5.625 16.8421 5.625ZM7.03125 1.45537C9.43927 1.7947 11.2969 3.85055 11.2969 6.32812V16.5239C10.2659 15.2518 8.75058 14.3825 7.03125 14.1945V1.45537ZM4.21875 4.21875H5.625V15.5625H6.32812C8.69639 15.5625 10.6826 17.2124 11.1779 19.4126C9.95433 18.7316 8.58145 18.375 7.15786 18.375H4.21875V4.21875ZM22.5938 21.1875H16.8481C15.478 21.1875 14.1843 21.6847 13.1731 22.5938H10.8269C9.81567 21.6847 8.52202 21.1875 7.15186 21.1875H1.40625V7.03125H2.8125V19.7812H7.15786C9.69675 19.7812 11.2132 21.1253 11.334 21.1875H12.666C12.7859 21.1258 14.3071 19.7812 16.8421 19.7812H21.1875V8.4375H22.5938V21.1875Z"
                                    fill="#FCC6E2"
                                />
                            </g>
                        </svg>
                    </div>
                </div>
                <div class="card__content px-md-4 px-3">
                    <div class="card__title mb-0 mb-lg-2">
                        <a href="{{ $service->url }}">
                            <p class="fs-4 text-dark">{!! BaseHelper::clean($service->name) !!}</p>
                        </a>
                    </div>
                    @if($service->description)
                        <p class="text-300 mb-lg-auto mb-md-4 mb-3">
                            {!! BaseHelper::clean($service->description) !!}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
