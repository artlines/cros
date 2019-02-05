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
    Grid,
    Typography,
    Paper,
    Collapse,
} from "@material-ui/core";
import {Edit as EditIcon} from '@material-ui/icons';
import map from "lodash/map";
import find from "lodash/find";
import RoomTypeForm from "./RoomTypeForm";
import ParticipationClass from "../../../containers/ParticipationClass";
import FabButton from "../../utils/FabButton";

class RoomTypeTable extends React.Component {
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

    handleDescriptionSuccess = id => {
        this.setState({[`expanded_${id}`]: false});
        this.props.load();
    };

    render() {
        const { open, initialValues } = this.state.form;
        const { items } = this.props;

        return (
            <React.Fragment>
                <Grid container spacing={16}>
                    <Grid item xs={12}>
                        <Grid container justify={`space-between`}>
                            <Grid item>
                                <Typography variant={`h4`} gutterBottom>Типы комнат</Typography>
                            </Grid>
                            <Grid item>
                                <FabButton title={`Добавить`} onClick={this.openForm}/>
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid item xs={12}>
                        <Paper>
                            <Table>
                                <TableHead>
                                    <TableRow>
                                        <TableCell>Наименование</TableCell>
                                        <TableCell>Стоимость</TableCell>
                                        <TableCell>Кол-во мест</TableCell>
                                        <TableCell>Класс участия</TableCell>
                                        <TableCell align={`right`}>Действия</TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {map(items, item =>
                                        <React.Fragment key={item.id}>
                                            <TableRow style={{borderTop: '1px solid #eee'}}>
                                                <TableCell>{item.title}</TableCell>
                                                <TableCell>{item.cost}</TableCell>
                                                <TableCell>{item.max_places}</TableCell>
                                                <TableCell><ParticipationClass id={item.participation_class_id}/></TableCell>
                                                <TableCell align={`right`}>
                                                    <Button onClick={() => this.openForm(item.id)}>
                                                        <EditIcon/>
                                                    </Button>
                                                </TableCell>
                                            </TableRow>
                                        </React.Fragment>
                                    )}
                                </TableBody>
                            </Table>
                        </Paper>
                    </Grid>
                    <Grid item xs={12}>
                        <Grid container justify={`center`}>
                            <Grid item xs={12} md={6}>
                                <Collapse in={open} unmountOnExit={true}>
                                    <RoomTypeForm
                                        initialValues={initialValues}
                                        onClose={this.closeForm}
                                        onSuccess={this.handleSuccess}
                                    />
                                </Collapse>
                            </Grid>
                        </Grid>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

RoomTypeTable.propTypes = {
    load: PropTypes.func.isRequired,

    items: PropTypes.array.isRequired,
    isFetching: PropTypes.bool.isRequired,
};

const mapStateToProps = state =>
    ({
        ...state.abode.room_type,
    });

export default connect(mapStateToProps)(RoomTypeTable);