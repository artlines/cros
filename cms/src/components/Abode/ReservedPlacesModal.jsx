import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogActions,
    DialogContent,
    Grid,
    TextField,
    Typography,
} from '@material-ui/core';
import map from "lodash/map";
import sortBy from "lodash/sortBy";
import API from '../../libs/api';
import LinearProgress from '../utils/LinearProgress';
import ErrorMessage from '../utils/ErrorMessage';
import abode from "../../actions/abode";

const api = new API();

class ReservedPlacesModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
            submitting: false,
            data: null,
            submitError: false,
        };
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { items } = this.props;
        const { open } = this.state;

        if (open !== prevState.open && open) {
            this.setState({ data: map(sortBy(items, 'room_type_title'), i => ({
                    room_type_id:       i.room_type_id,
                    room_type_title:    i.room_type_title,
                    count:              i.reserved,
            })) });
        }
    }

    handleChange = index => event => {
        const value = event.target.value;
        const { data } = this.state;
        data[index].count = value;
        this.setState({data});
    };

    handleSubmit = event => {
        event.preventDefault();
        const { housing_id } = this.props;
        const { data } = this.state;

        this.setState({submitting: true});

        api.post(`housing/${housing_id}/reserve_places`, {reserves: data})
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit)
    };

    handleSuccessSubmit = () => {
        this.props.onSuccessSubmit();
        this.handleClose();
    };

    handleErrorSubmit = (err) => this.setState({submitting: false, submitError: err.message});

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({
        open: false,
        submitting: false,
        data: null,
        submitError: false,
    });

    render() {
        const { trigger, items } = this.props;
        const { open, submitting, data, submitError } = this.state;

        if (items.length === 0) return null;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
                <Dialog
                    open={open}
                    onClose={this.handleClose}
                    fullWidth={true}
                    maxWidth={'xs'}
                >
                    <DialogTitle>
                        Резервирование мест
                    </DialogTitle>
                    <DialogContent>
                        <form onSubmit={this.handleSubmit} id={"reserved_places-form"}>
                            <LinearProgress show={submitting}/>
                            <Grid container spacing={16}>
                                {map(sortBy(data), (i, index) =>
                                    <React.Fragment key={index}>
                                        <Grid item xs={8}>
                                            <Typography>{i.room_type_title}</Typography>
                                        </Grid>
                                        <Grid item xs={4}>
                                            <TextField
                                                required
                                                type={`number`}
                                                value={i.count}
                                                onChange={this.handleChange(index)}
                                                inputProps={{ min: "0", max: "1000", step: "1" }}
                                            />
                                        </Grid>
                                    </React.Fragment>
                                )}
                            </Grid>
                        </form>
                        {submitError && <ErrorMessage description={submitError} extended={true}/>}
                    </DialogContent>
                    <DialogActions>
                        <Button
                            color={"primary"}
                            disabled={submitting}
                            onClick={this.handleClose}
                        >
                            Закрыть
                        </Button>
                        <Button
                            variant={"contained"}
                            color={"primary"}
                            form={"reserved_places-form"}
                            type={"submit"}
                            disabled={submitting}
                        >
                            Применить
                        </Button>
                    </DialogActions>
                </Dialog>
            </React.Fragment>
        );
    }
}

ReservedPlacesModal.propTypes = {
    /** Trigger */
    trigger: PropTypes.node.isRequired,

    /** Will called after success submit */
    onSuccessSubmit: PropTypes.func.isRequired,

    /** Housing ID */
    housing_id: PropTypes.number.isRequired,

    /** Reserved places info by room type ID */
    items: PropTypes.arrayOf(
        PropTypes.shape({
            room_type_id:       PropTypes.number.isRequired,
            room_type_title:    PropTypes.string.isRequired,
            reserved:           PropTypes.number.isRequired,
        }),
    ),
};

const mapDispatchToProps = dispatch =>
    ({
        fetchHousings: () => dispatch(abode.fetchHousings()),
    });

export default connect(null, mapDispatchToProps)(ReservedPlacesModal);