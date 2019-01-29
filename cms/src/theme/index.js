import { createMuiTheme } from "@material-ui/core/styles";

export default createMuiTheme({
    overrides: {
        MuiGrid: {
            container: {
                
            }
        },
        MuiTableCell: {
            root: {
                padding: '4px 12px',
            },
        },
    }
});