import React from "react";
import PropTypes from "prop-types";
import {connect} from 'react-redux';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
    Button,
    Typography,
    Grid,
    Paper,
} from "@material-ui/core";
import {Edit as EditIcon} from '@material-ui/icons';
import map from "lodash/map";
import ParticipationClassForm from "./ParticipationClassForm";
import find from "lodash/find";
import FabButton from "../../utils/FabButton";

class ParticipationClassTable extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            form: {
                open: false,
                initialValues: {},
            },
        };
    }

    componentDidMount = () => {
        this.props.load();
    };

    openForm = (id) => {
        const { items } = this.props;
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: id ? find(items, {id}) : {},
            }
        });
    };
    closeForm = () => this.setState({form: {...this.state.form, open: false, initialValues: {}}});

    handleSuccess = () => {
        this.closeForm();
        this.props.load();
    };

    render() {
        const { open, initialValues } = this.state.form;
        const { items } = this.props;

        return (
            <React.Fragment>
                <Grid container justify={`space-between`}>
                    <Grid item>
                        <Typography variant={`h4`} gutterBottom>Классы участия</Typography>
                    </Grid>
                    <Grid item>
                        <FabButton title={`Добавить`} onClick={this.openForm}/>
                    </Grid>
                </Grid>
                <Paper>
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>Наименование</TableCell>
                                <TableCell align={`right`}>Действия</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {map(items, item =>
                                <TableRow key={item.id}>
                                    <TableCell>{item.title}</TableCell>
                                    <TableCell align={`right`}>
                                        <Button onClick={() => this.openForm(item.id)}>
                                            <EditIcon/>
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </Paper>
                <ParticipationClassForm
                    initialValues={initialValues}
                    open={open}
                    onClose={this.closeForm}
                    onSuccess={this.handleSuccess}
                />
            </React.Fragment>
        );
    }
}

ParticipationClassTable.propTypes = {
    load: PropTypes.func.isRequired,

    items: PropTypes.array.isRequired,
    isFetching: PropTypes.bool.isRequired,
};

const mapStateToProps = state =>
    ({
        ...state.abode.participation_class,
    });

export default connect(mapStateToProps)(ParticipationClassTable);