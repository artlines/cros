import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Table,
    TableHead,
    TableBody,
    TableFooter,
    TableRow,
    TableCell,
    TextField,
    InputAdornment,
    Grid,
} from '@material-ui/core';
import map from "lodash/map";
import sum from "lodash/sum";
import some from "lodash/some";
import every from "lodash/every";
import LinearProgress from '../utils/LinearProgress';
import ErrorMessage from '../utils/ErrorMessage';
import RoomType from '../../containers/RoomType';
import Money from "../utils/Money";
import API from '../../libs/api';

const api = new API();

class MakeInvoiceModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
            costs: [],
            submitting: false,
            submitError: null,
            showForm: true,
        };
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { open } = this.state;
        const { update, organization_id, members: { isFetching, items } } = this.props;

        /** When dialog is open then fetch members */
        if (prevState.open !== open && open) {
            update({conference_organization_id: organization_id});
        }

        /** If fetching is finished then update costs in state */
        if (open && !isFetching && prevProps.members.isFetching) {
            const costs = map(items, i => i.room_type_cost);

            let submitError = null;
            let showForm    = true;

            /** Check for members count */
            if (items.length === 0) {
                submitError = 'У организации отсутствуют участники.';
                showForm    = false;
            }

            /** Check that each member was settled */
            if (!every(items, i => i.place.room_num)) {
                submitError = 'У организации имеются нерасселенные участники.';
                showForm    = false;
            }

            /** Check that exist at least one representative member */
            if (!some(items, i => i.representative)) {
                submitError = 'Ни один участник от организации не указан как представитель.';
                showForm    = false;
            }

            this.setState({costs, submitError, showForm})
        }
    };

    handleCostChange = index => event => {
        const { value } = event.target;
        let costs = [...this.state.costs];
        costs[index] = Number(value);
        this.setState({costs, submitError: null});
    };

    handleSubmit = event => {
        event.preventDefault();
        const { organization_id: conference_organization_id } = this.props;
        const amount = sum(this.state.costs);

        this.setState({submitting: true});

        api.post(`invoice/new`, {amount, conference_organization_id})
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit);
    };

    handleSuccessSubmit = () => {
        this.props.onSuccess();
        this.handleClose();
    };

    handleErrorSubmit = (err) => this.setState({submitting: false, submitError: err.message});

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false, submitting: false, submitError: null});

    render() {
        const { trigger, organization_name, members: { isFetching, items } } = this.props;
        const { open, costs, submitting, submitError, showForm } = this.state;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
                <Dialog
                    open={open}
                    onClose={this.handleClose}
                    fullWidth={true}
                    maxWidth={'sm'}
                >
                    <DialogTitle>
                        <Grid
                            container
                            spacing={0}
                            justify={`space-between`}
                            alignItems={`center`}
                        >
                            <Grid item>
                                Выставление счета для {organization_name}
                            </Grid>
                            <Grid item>
                                {/*<FabButton title={`Добавить счет`} onClick={this.openForm}/>*/}
                            </Grid>
                        </Grid>
                    </DialogTitle>
                    <DialogContent>
                        <LinearProgress show={isFetching || submitting}/>
                        {showForm && items.length > 0 &&
                            <Table>
                                <TableHead>
                                    <TableRow>
                                        <TableCell>ФИО</TableCell>
                                        <TableCell>Тип комнаты</TableCell>
                                        <TableCell align={`right`} style={{width: 140}}>Стоимость</TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {map(items, (item, i) =>
                                        <TableRow key={i}>
                                            <TableCell>{item.last_name} {item.first_name}</TableCell>
                                            <TableCell>
                                                <RoomType id={item.room_type_id}/>
                                            </TableCell>
                                            <TableCell>
                                                <TextField
                                                    value={costs[i] || 0}
                                                    type={`number`}
                                                    onChange={this.handleCostChange(i)}
                                                    InputProps={{
                                                        endAdornment: <InputAdornment position="start">₽</InputAdornment>,
                                                    }}
                                                />
                                            </TableCell>
                                        </TableRow>
                                    )}
                                    </TableBody>
                                <TableFooter>
                                    <TableRow>
                                        <TableCell/>
                                        <TableCell align={`right`} style={{fontSize: '1rem'}}>
                                            Итого
                                        </TableCell>
                                        <TableCell style={{fontSize: '1rem'}}><Money value={sum(costs)}/></TableCell>
                                    </TableRow>
                                </TableFooter>
                            </Table>
                        }
                        {submitError && <ErrorMessage description={submitError} extended={true}/>}
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={this.handleClose}>Закрыть</Button>
                        <Button
                            variant={"contained"}
                            color={"primary"}
                            type={"submit"}
                            disabled={!!(isFetching || submitting || submitError)}
                            onClick={this.handleSubmit}
                        >
                            Выставить счет
                        </Button>
                    </DialogActions>
                </Dialog>
            </React.Fragment>
        );
    }
}

MakeInvoiceModal.propTypes = {
    /** Trigger to open modal */
    trigger:    PropTypes.node.isRequired,

    /** Organization info */
    organization_id:    PropTypes.number.isRequired,
    organization_name:  PropTypes.string.isRequired,

    update:     PropTypes.func,
    onSuccess:  PropTypes.func,
};

MakeInvoiceModal.defaultProps = {
    update:     () => {},
    onSuccess:  () => {},
};

const mapStateToProps = state =>
    ({
        members: state.participating.member,
    });

export default connect(mapStateToProps)(MakeInvoiceModal);