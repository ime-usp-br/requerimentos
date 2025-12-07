import React from 'react';
import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import { ThemeProvider } from '@mui/material/styles';
import '@fontsource/roboto/300.css';
import '@fontsource/roboto/400.css';
import '@fontsource/roboto/500.css';
import '@fontsource/roboto/700.css';

import { DialogProvider } from "./Context/useDialogContext"
import { UserProvider } from "./Context/useUserContext"
import theme from './styles/theme.js';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true })
    return pages[`./Pages/${name}.jsx`]
  },
  setup({ el, App, props }) {
    const root = createRoot(el)

    root.render(
      <DialogProvider>
        <App {...props}>
          {({ Component, key, props }) => (
            <ThemeProvider theme={theme}>
                <UserProvider>
                    <Component key={key} {...props} />
                </UserProvider>
            </ThemeProvider>
          )}
        </App>
      </DialogProvider>
    )
  },
})
