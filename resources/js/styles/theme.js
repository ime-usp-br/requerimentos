import { createTheme } from "@mui/material/styles";

// let theme = createTheme({
//     palette: {
//         primary: '#0c3483',
//         orange:  '#FF9305',
//         green:   '#0BC294',
//         purple:  '#586EFF',
//     }
// }),

let theme = createTheme({
    palette: {
        blue: {
            dark: '#023373',
            main: '#7CB4FD',
            light: '#F1F4F7'
        },
        indigo: { main: '#6610f2' },
        purple: { main: '#6f42c1' },
        pink: { main: '#e83e8c' },
        red: { main: '#dc3545' },
        orange: {
            dark: '#F2A03D',
            main: '#F2DF80'
        },
        yellow: { main: '#ffc107' },
        green: { main: '#28a745' },
        teal: { main: '#20c997' },
        cyan: { main: '#17a2b8' },
        white: { main: '#ffffff' },
        gray: { main: '#6c757d' },
        primary: { main: '#0170B9' },
        secondary: { main: '#6c757d' },
        success: { main: '#28a745' },
        info: { main: '#17a2b8' },
        warning: { main: '#ffc107' },
        danger: { main: '#dc3545' },
        light: { main: '#f8f9fa' },
        dark: { main: '#343a40' },
    }
});

export default theme;
