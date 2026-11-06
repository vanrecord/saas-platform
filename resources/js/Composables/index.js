import { useDark, useToggle } from '@vueuse/core'

export const isDark = useDark({
  selector: 'body',
  attribute: 'color-scheme',
  valueDark: 'dark',
  valueLight: 'light'
})
export const toggleDarkMode = useToggle(isDark)

