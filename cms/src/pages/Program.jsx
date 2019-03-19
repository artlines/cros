import React from 'react';
import {connect} from 'react-redux';
import {
    Collapse,
    Paper,
    Tab,
    Tabs,
} from '@material-ui/core';
import {
    RecordVoiceOver,
    PersonAdd,
} from '@material-ui/icons';
import SpeedDialMenu from '../components/utils/SpeedDialMenu';
import ProgramMemberForm from '../components/Conference/ProgramMemberForm';
import participating from "../actions/participating";

class Program extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            currentTab: 0,
            isFormOpen: false,
        };

        this.actions = [
            {
                icon: <RecordVoiceOver/>,
                title: 'Добавить спикера',
                tooltipOpen: true,
                onClick: () => this.setState({isFormOpen: true}),
            },
            {
                icon: <PersonAdd/>,
                title: 'Добавить члена комитета',
                tooltipOpen: true,
                onClick: () => this.setState({isFormOpen: true}),
            },
        ];
    }

    componentDidMount() {
        const { fetchMembers } = this.props;
        fetchMembers();
    }

    render() {
        const { isFormOpen, currentTab } = this.state;

        return (
            <React.Fragment>
                <Paper>
                    <Tabs
                        value={currentTab}
                        variant={`fullWidth`}
                        onChange={(event, tab) => this.setState({currentTab: tab})}
                        indicatorColor="primary"
                        textColor="primary"
                    >
                        <Tab label={`Комитет`}/>
                        <Tab label={`Спикеры`}/>
                        <Tab disabled label={`Расписание`}/>
                    </Tabs>
                </Paper>
                <Collapse in={isFormOpen} unmountOnExit={true}>
                    <ProgramMemberForm/>
                </Collapse>
                <SpeedDialMenu actions={this.actions}/>
            </React.Fragment>
        );
    }
}

const mapDispatchToProps = dispatch =>
    ({
        fetchMembers: (data = {}) => dispatch(participating.fetchMembers(data)),
    });

export default connect(null, mapDispatchToProps)(Program);