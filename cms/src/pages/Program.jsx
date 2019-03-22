import React from 'react';
import {connect} from 'react-redux';
import {
    Collapse,
    Grid,
    Paper,
    Tab,
    Tabs,
} from '@material-ui/core';
import {
    RecordVoiceOver,
    PersonAdd,
} from '@material-ui/icons';
import SpeedDialMenu from '../components/utils/SpeedDialMenu';
import ProgramMemberForm from '../components/Program/ProgramMemberForm';
import participating from "../actions/participating";
import program from "../actions/program";
import ProgramMemberTable from "../components/Program/ProgramMemberTable";

class Program extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            currentTab: 0,
            isFormOpen: false,
            initialValues: {},
        };

        this.actions = [
            {
                icon: <RecordVoiceOver/>,
                title: 'Добавить спикера',
                tooltipOpen: true,
                onClick: () => this.setState({
                    isFormOpen: true,
                    initialValues: {type: 'speaker'},
                }),
            },
            {
                icon: <PersonAdd/>,
                title: 'Добавить члена комитета',
                tooltipOpen: true,
                onClick: () => this.setState({
                    isFormOpen: true,
                    initialValues: {type: 'committee'},
                }),
            },
        ];
    }

    componentDidMount() {
        this.props.fetchMembers();
        this.props.fetchSpeakers();
        this.props.fetchCommittee();
    }

    handleTabChange = (event, tab) => {
        this.setState({currentTab: tab, isFormOpen: false});
    };

    handleFormEdit = (data) => {
        this.setState({
            isFormOpen: true,
            initialValues: data,
        });
    };

    handleFormClose = () => {
        this.setState({isFormOpen: false});
    };

    handleFormSuccessSubmit = () => {
        this.props.fetchSpeakers();
        this.props.fetchCommittee();
    };

    render() {
        const { speaker, committee } = this.props;
        const { initialValues, isFormOpen, currentTab } = this.state;

        return (
            <React.Fragment>
                <Grid container spacing={16}>
                    <Grid item xs={12}>
                        <Paper>
                            <Tabs
                                value={currentTab}
                                variant={`fullWidth`}
                                onChange={this.handleTabChange}
                                indicatorColor="primary"
                                textColor="primary"
                            >
                                <Tab label={`Комитет`}/>
                                <Tab label={`Спикеры`}/>
                                <Tab disabled label={`Расписание`}/>
                            </Tabs>
                        </Paper>
                    </Grid>
                    <Grid item xs={12}>
                        <Collapse in={isFormOpen} unmountOnExit={true}>
                            <ProgramMemberForm
                                initialValues={initialValues}
                                onClose={this.handleFormClose}
                                onSuccess={this.handleFormSuccessSubmit}
                            />
                        </Collapse>
                    </Grid>
                    <Grid item xs={12}>
                        <Paper>
                            {currentTab === 0 &&
                            <ProgramMemberTable onEdit={this.handleFormEdit} items={speaker.items}/>
                            }
                            {currentTab === 1 &&
                            <ProgramMemberTable onEdit={this.handleFormEdit} items={committee.items}/>
                            }
                        </Paper>
                    </Grid>
                </Grid>
                <SpeedDialMenu actions={this.actions} hidden={isFormOpen}/>
            </React.Fragment>
        );
    }
}

const mapStateToProps = state =>
    ({
        speaker: state.program.speaker,
        committee: state.program.committee,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchMembers: (data = {}) => dispatch(participating.fetchMembers(data)),
        fetchSpeakers: () => dispatch(program.fetchSpeakers()),
        fetchCommittee: () => dispatch(program.fetchCommittee()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Program);