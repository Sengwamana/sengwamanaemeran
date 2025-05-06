import '@tabler/core/core/js/src/autosize'
import '@tabler/core/core/js/src/dropdown'
import '@tabler/core/core/js/src/tooltip'
import '@tabler/core/core/js/src/popover'
import '@tabler/core/core/js/src/switch-icon'
import '@tabler/core/core/js/src/tab'
import * as bootstrap from 'bootstrap'
import * as tabler from '@tabler/core/core/js/src/tabler'

globalThis.bootstrap = bootstrap
globalThis.tabler = tabler

import setupProgress from './base/progress'

setupProgress({
    showSpinner: true,
})
