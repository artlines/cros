import React from "react";
import {
    Button,
    Grid,
} from "@material-ui/core";

class Report extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {

        return (
            <Grid container spacing={16}>
                <Grid item xs={12} md={4}>
                    <Button
                        variant={`contained`}
                        color={`primary`}
                        size={`large`}
                        onClick={() => window.open('/api/v1/report/summary', '_blank').focus()}
                    >
                        Сводный отчет
                    </Button>
                </Grid>
            </Grid>
        );
    }
}

export default Report;