import React from "react";
import {
    Grid,
    Paper,
    Typography,
} from "@material-ui/core";
import ParticipantClasses from "../../components/Abode/ParticipantClasses";

class Settings extends React.Component {
    render() {
        return (
            <Grid container spacing={24}>
                <Grid item xs={12} sm={6}>
                    <Typography variant={`h4`} gutterBottom>Классы участия</Typography>
                    <Paper>
                        <ParticipantClasses/>
                    </Paper>
                </Grid>
                <Grid item xs={12} sm={6}>
                    <Typography variant={`h4`} gutterBottom>Классы участия</Typography>
                    <Paper>
                        <ParticipantClasses/>
                    </Paper>
                </Grid>
                <Grid item xs={12} sm={6}>
                    <Typography variant={`h4`} gutterBottom>Классы участия</Typography>
                    <Paper>
                        <ParticipantClasses/>
                    </Paper>
                </Grid>
                <Grid item xs={12} sm={6}>
                    <Typography variant={`h4`} gutterBottom>Классы участия</Typography>
                    <Paper>
                        <ParticipantClasses/>
                    </Paper>
                </Grid>
            </Grid>
        );
    }
}

export default Settings;