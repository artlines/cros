import React from "react";
import {connect} from 'react-redux';
import {
    Paper,
    Typography,
    Grid,
} from "@material-ui/core";
import SummaryInformation from "../components/Abode/SummaryInformation";
import abode from "../actions/abode";

class Home extends React.Component {
    constructor(props) {
        super(props);

        this.updateInterval = null;
    }

    componentDidMount() {
        this.props.fetchSummaryInformation();
        this.props.fetchRoomTypes();
        this.updateInterval = setInterval(() => {
            this.props.fetchSummaryInformation();
        }, 60000);
    }

    componentWillUnmount() {
        this.updateInterval = null;
    }

    render() {
        const { roles, summary_information } = this.props;

        return (
            <Grid container spacing={16}>
                {roles.includes('ROLE_SETTLEMENT_MANAGER') &&
                <Grid item xs={12}>
                    <Typography gutterBottom variant={`h5`}>Сводная информация</Typography>
                    <Paper>
                        <SummaryInformation {...summary_information}/>
                    </Paper>
                </Grid>
                }
            </Grid>
        );
    }
}

const mapStateToProps = state =>
    ({
        summary_information: state.abode.summary_information,
        roles: state.system.user.roles,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchSummaryInformation: () => dispatch(abode.fetchSummaryInformation()),
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Home);