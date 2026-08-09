@if($orders->total() < 1)
    <h4 class="text-danger">{{ __('Nothing Found') }}</h4>
@else
    @foreach($orders as $order)
        @php $rating =  \App\Models\Rating::select('id','order_id','rating')->where('order_id',$order->id)->where('sender_type',2)->first(); @endphp

        <div class="myOrder-single bg-white padding-20 radius-10">
            <div class="myOrder-single-item">
                <div class="myOrder-single-flex">
                    <div class="myOrder-single-content">
                        <span class="myOrder-single-content-id">#000{{ $order->id }}</span>
                        <h4 class="myOrder-single-content-title mt-2">
                            @if($order->is_project_job == 'project')
                                <a href="{{ route('client.order.details',$order->id) }}"> {{ $order?->project->title ?? '' }} </a>
                            @elseif($order->is_project_job == 'job')
                                <a href="{{ route('client.order.details',$order->id)  }}">{{ $order?->job->title ?? '' }}</a>
                            @else
                               {{ __('Custom order')}}
                            @endif
                        </h4>
                        <div class="myOrder-single-content-btn flex-btn mt-3">
                            <x-order.order-status :status="$order->status" />
                            <x-order.is-custom :isCustom="$order->is_project_job" />
                            <x-order.payment-verify :paymentVerifyCheck="$order" />
                        </div>
                    </div>

                    <span class="myOrder-single-content-time">
                        @php
                            \Carbon\Carbon::setLocale('en');
                        @endphp

                        <p class="myOrder-single-content-time">
                            {{ $order->created_at->diffForHumans() }}
                        </p>

                        @php
                            \Carbon\Carbon::setLocale(app()->getLocale());
                        @endphp
                    </span>
                </div>
            </div>
            <div class="myOrder-single-item">
                <div class="myOrder-single-block">
                    <div class="myOrder-single-block-item">
                        <div class="myOrder-single-block-item-content">
                            @if($order->is_fixed_hourly == 'hourly')
                                <span class="myOrder-single-block-subtitle">{{ __('Hourly rate') }}</span>
                                <h6 class="myOrder-single-block-title mt-2">{{ float_amount_with_currency_symbol($order?->job->hourly_rate) }}</h6>
                            @else
                                <span class="myOrder-single-block-subtitle">{{ __('Order budget') }}</span>
                                <h6 class="myOrder-single-block-title mt-2">{{ float_amount_with_currency_symbol($order->price) }}
                                    <x-order.is-funded :isFunded="$order->payment_status" :paymentGateway="$order->payment_gateway"/>
                                </h6>
                            @endif
                        </div>
                    </div>
                    @if($order->delivery_time)
                    <div class="myOrder-single-block-item">
                        <div class="myOrder-single-block-item-content">
                            <span class="myOrder-single-block-subtitle">{{ __('Delivery Time') }}</span> <br>
                            <x-order.deadline :deadline="$order->delivery_time ?? '' " />
                        </div>
                    </div>
                    @endif
                    <div class="myOrder-single-block-item">
                      <div class="myOrder-single-block-item-author position-relative" style="display:inline-block;">
                            <x-order.profile-image :image="$order?->freelancer->image" />

                            {{-- Online/Offline Dot --}}
                            <span class="profile-status-icon" style="
                                position: absolute;
                                bottom: 2px;
                                right: 36px;
                                top: 1px;
                                width: 14px;
                                height: 14px;
                                border-radius: 50%;
                                border: 2px solid #fff;
                                background-color: {{ Cache::has('user_is_online_' . $order?->freelancer->id) ? '#28a745' : '#6c757d' }};">
                            </span>
                        </div>
                        <div class="freelancer-info">
                            <span class="freelancer-type">
                                @if($order?->freelancer->user_type == 1)
                                    Client
                                @elseif($order?->freelancer->user_type == 2)
                                    Freelancer
                                @else
                                    Unknown
                                @endif

                                {{-- Verified / Unverified Badge --}}
                                @if($order?->freelancer?->user_verified_status == 1)
                                    <i class="fas fa-circle-check" title="Verified"></i>
                                @else
                                    <i class="fas fa-circle-xmark" title="Unverified"></i>
                                @endif
                            </span>

                            <h6 class="myOrder-single-block-title mt-1">
                                {{ $order?->freelancer->first_name }} {{ $order?->freelancer->last_name }}
                            </h6>

                            {{-- Rating --}}
                            @if(!empty($rating?->rating))
                                <div class="freelancer-rating">
                                    {!! freelancer_rating($order?->freelancer->id) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="myOrder-single-item">
                <div class="myOrder-single-flex flex-between">
                    <div class="btn-wrapper flex-btn">
                        <a href="{{ route('client.order.details',$order->id) }}" class="btn-profile btn-bg-1">{{ __('View Order') }}</a>
                        @if($order->status == 3)
                            @if($order?->user?->is_suspend !=1)
                                <a href="{{ route('client.order.rating',$order->id) }}" class="btn-profile btn-outline-gray">{{ __('Submit Review') }}</a>
                                <a href="{{ route('client.order.invoice.generate',$order->id) }}" class="btn-profile btn-outline-gray">{{ __('Invoice') }}</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <x-pagination.laravel-paginate :allData="$orders" />
@endif
