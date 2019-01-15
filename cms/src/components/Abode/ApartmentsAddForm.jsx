import React from "react";
import PropTypes from "prop-types";
import {connect} from 'react-redux';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    TextField,
    MenuItem,
    Typography,
} from '@material-ui/core';
import find from 'lodash/find';
import map from 'lodash/map';
import times from 'lodash/times';
import isEqual from 'lodash/isEqual';
import isEmpty from 'lodash/isEmpty';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";

const api = new API();

class ApartmentsAddForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                num_from: '',
                num_to: '',
                floor: '',
                type: '',
                housing_id: 0,
                room_types: {},
            },
            errors: {},
            submitting: false,
            submitError: false,
            rooms_count: 0,
        };
    }

    componentDidMount() {
        const { initialValues } = this.props;
        const { values } = this.state;
        !!initialValues && this.setState({values: {...values, ...initialValues}});
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { open, initialValues } = this.props;
        const { values } = this.state;

        /**
         * Check for updates initialValues
         */
        if (!isEqual(prevProps.initialValues, initialValues) || (open === true && prevProps.open !== open)) {
            this.setState({values: {...values, ...initialValues}})
        }
    }

    handleChange = (field, index = null) => event => {
        const { values, errors } = this.state;

        const update = index !== null ? { [field]: {...values[field], [index]: event.target.value }} : { [field]: event.target.value };

        index !== null ? (errors[field] && delete(errors[field][index])) : delete(errors[field]);
        isEmpty(errors[field]) && delete(errors[field]);

        this.setState({
            values: {
                ...values,
                ...update
            },
            errors,
        });
    };

    handleCancel = () => {
        this.props.onClose();
        this.setState({values: {}, errors: {}});
    };

    handleSubmit = event => {
        event.preventDefault();
        const { values } = this.state;

        if (!this.validate()) {
            return;
        }

        this.setState({
            submitting: true,
            submitError: false,
        });

        /** Create or update entity */
        api.post(`apartment/generate`, values)
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit);
    };

    handleSuccessSubmit = () => {
        this.setState({submitting: false});
        this.props.onSuccess();
    };

    handleErrorSubmit = (err) => {
        this.setState({submitting: false, submitError: err.message});
    };

    validate = () => {
        const { apartment_types } = this.props;
        const { values, errors } = this.state;
        const currentApartmentType = find(apartment_types, {id: values.type});
        let errorsUpdate = {};

        /**
         * Check room_types
         */
        if (Object.values(values.room_types).length !== currentApartmentType.max_rooms) {
            times(currentApartmentType.max_rooms, i => {
                if (!values.room_types[i]) {
                    if (!errorsUpdate.room_types) {
                        errorsUpdate.room_types = {};
                    }
                    errorsUpdate.room_types[i] = 'Укажите тип комнаты';
                }
            });
        }

        this.setState({
            errors: {
                ...errors,
                ...errorsUpdate
            }
        });

        if (!isEmpty(errorsUpdate)) {
            return false;
        }

        return true;
    };

    render() {
        const { open, apartment_types, room_types } = this.props;
        const { values, errors, submitting, submitError } = this.state;

        return (
            <Dialog
                open={open}
                fullWidth={true}
                maxWidth={"sm"}
            >
                <DialogTitle>Массовое добавление номеров</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"apartments-add-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Номера с"}
                                    type={"number"}
                                    inputProps={{ min: 1, max: 10000, step: 1 }}
                                    value={values.num_from}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChange('num_from')}
                                    error={!!errors.num_from}
                                    helperText={errors.num_from}
                                />
                            </Grid>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Номера по"}
                                    type={"number"}
                                    inputProps={{ min: 1, max: 10000, step: 1 }}
                                    value={values.num_to}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChange('num_to')}
                                    error={!!errors.num_to}
                                    helperText={errors.num_to}
                                />
                            </Grid>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Этаж"}
                                    type={"number"}
                                    inputProps={{ min: 1, max: 100, step: 1 }}
                                    value={values.floor}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChange('floor')}
                                    error={!!errors.floor}
                                    helperText={errors.floor}
                                />
                            </Grid>

                            <Grid item xs={12}>
                                <TextField
                                    required
                                    label={"Тип номера"}
                                    value={values.type}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChange('type')}
                                    error={!!errors.type}
                                    helperText={errors.type}
                                    select={true}
                                >
                                    {map(apartment_types, at =>
                                        <MenuItem key={at.id} value={at.id}>{at.title}</MenuItem>
                                    )}
                                </TextField>
                            </Grid>
                        </Grid>

                        {values.type &&
                        <Grid container spacing={16}>
                            <Grid item xs={12}> </Grid>
                            <Grid item xs={12}>
                                <Typography>Укажите типы комнат в номере</Typography>
                            </Grid>
                            {times(find(apartment_types, {id: values.type}).max_rooms, i =>
                                <Grid key={i} item xs={12}>
                                    <TextField
                                        required
                                        label={`Тип комнаты #${i+1}`}
                                        value={values.room_types[i]}
                                        margin={"dense"}
                                        fullWidth
                                        variant={"outlined"}
                                        onChange={this.handleChange('room_types', i)}
                                        error={errors.room_types && !!errors.room_types[i] || false}
                                        helperText={errors.room_types && errors.room_types[i] || ''}
                                        select={true}
                                    >
                                        {map(room_types, rt =>
                                            <MenuItem key={rt.id} value={rt.id}>{rt.title}</MenuItem>
                                        )}
                                    </TextField>
                                </Grid>
                            )}
                        </Grid>
                        }

                    </form>
                    {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
                </DialogContent>
                <DialogActions>
                    <Button
                        color={"primary"}
                        disabled={submitting}
                        onClick={this.handleCancel}
                    >
                        Отмена
                    </Button>
                    <Button
                        variant={"contained"}
                        color={"primary"}
                        form={"apartments-add-form"}
                        type={"submit"}
                        disabled={submitting}
                    >
                        Добавить
                    </Button>
                </DialogActions>
            </Dialog>
        );
    }
}

ApartmentsAddForm.propTypes = {
    /**
     * Is form open?
     */
    open: PropTypes.bool.isRequired,

    /**
     * Initial form values
     */
    initialValues: PropTypes.object,

    /**
     * Fired when form need to be closed
     */
    onClose: PropTypes.func.isRequired,

    /**
     * Fired when form success submitted
     */
    onSuccess: PropTypes.func.isRequired,
};

const mapStateToProps = state =>
    ({
        apartment_types: state.abode.apartment_type.items,
        room_types: state.abode.room_type.items,
    });

export default connect(mapStateToProps)(ApartmentsAddForm);