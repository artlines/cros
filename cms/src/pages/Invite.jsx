import React from 'react';
import {connect} from 'react-redux';
import {
    Grid,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Typography,
} from '@material-ui/core';
import FabButton from '../components/utils/FabButton';
import InviteForm from "../components/Organization/InviteForm";
import participating from '../actions/participating';
import map from "lodash/map";

class Invite extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            form: {
                open: false,
                initialValues: {},
            },
        };
    }

    componentDidMount() {
        this.update();
    }

    update = () => {
        const { fetchOrganizations, user } = this.props;
        const data = { invited_by: user.id };
        fetchOrganizations(data);
    };

    openForm = () => this.setState({form: {...this.state.form, open: true}});
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

    render() {
        const { organization: { items, isFetching } } = this.props;
        const { form } = this.state;

        return (
            <React.Fragment>
                <InviteForm
                    open={form.open}
                    initialValues={form.initialValues}
                    onClose={this.closeForm}
                    onSuccess={this.update}
                />
                <Grid container spacing={16}>
                    <Grid xs={12} item>
                        <Grid container justify={`space-between`} alignItems={`center`}>
                            <Grid item>
                                <Typography variant={`h4`}>Рассылка приглашений</Typography>
                            </Grid>
                            <Grid item>
                                <FabButton
                                    title={`Создать приглашение`}
                                    onClick={this.openForm}
                                />
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid xs={12} item>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>ID</TableCell>
                                    <TableCell>Наименование</TableCell>
                                    <TableCell>Реквизиты</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {map(items, item =>
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            {item.id}
                                        </TableCell>
                                        <TableCell>
                                            {item.name}
                                        </TableCell>
                                        <TableCell>
                                            <div style={{whiteSpace: 'nowrap'}}><b>ИНН:</b> {item.inn}</div>
                                            <div style={{whiteSpace: 'nowrap'}}><b>КПП:</b> {item.kpp}</div>
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

const mapStateToProps = state =>
    ({
        user: state.system.user,
        organization: state.participating.conference_organization,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchOrganizations: (data) => {
            dispatch(participating.fetchConferenceOrganizations(data));
        },
    });

export default connect(mapStateToProps, mapDispatchToProps)(Invite);