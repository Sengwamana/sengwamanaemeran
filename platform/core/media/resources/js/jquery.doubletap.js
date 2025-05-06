;(function ($) {
    $.event.special.doubletap = {
        bindType: 'touchend',
        delegateType: 'touchend',

        handle: function (event) {
            let handleObj = event.handleObj,
                targetData = jQuery.data(event.target),
                now = new Date().getTime(),
                delta = targetData.lastTouch ? now - targetData.lastTouch : 0,
                delay = delay == null ? 300 : delay

            if (delta < delay && delta > 30) {
                targetData.lastTouch = null
                event.type = handleObj.origType
                ;['clientX', 'clientY', 'pageX', 'pageY'].forEach(function (property) {
                    event[property] = event.originalEvent.changedTouches[0][property]
                })

                handleObj.handler.apply(this, arguments)
            } else {
                targetData.lastTouch = now
            }
        },
    }
})(jQuery)
