import React from 'react';
import {
    Grid,
    Paper,
    Typography,
} from '@material-ui/core';
import ParticipantClasses from "../../components/Adobe/ParticipantClasses";

class Adobe extends React.PureComponent {
    render() {
        return (
            <Grid container spacing={16}>
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

export default Adobe;