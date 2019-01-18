import React from 'react';
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
    LinearProgress,
    FormControlLabel,
    FormGroup,
    Checkbox,
    Typography,
    Divider,
} from '@material-ui/core';
import {
    CheckBox as CheckBoxIcon,
    CheckBoxOutlineBlank as CheckBoxOutlineBlankIcon
} from '@material-ui/icons';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";
import filter from "lodash/filter";
import isEmpty from "lodash/isEmpty";
import isEqual from "lodash/isEqual";
import find from "lodash/find";
import some from "lodash/some";
import map from "lodash/map";

const api = new API();

class ChangeRoomsTypesForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {},
            errors: {},
            submitting: false,
            submitError: false,

            filterType: 0,
        };

        this.initialValues = {
            room_type: 0,
            rooms: [],
        };
    }

    componentDidMount() {
        const { initialValues } = this.props;
        const { values } = this.state;
        !!initialValues && this.setState({values: {...values, ...initialValues}});
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        console.log(`ChangeRoomsTypesForm::componentDidUpdate`);

        const { open, initialValues } = this.props;
        const { values } = this.state;

        /**
         * Check for updates initialValues
         */
        if (!isEqual(prevProps.initialValues, initialValues) || (open === true && prevProps.open !== open)) {
            this.setState({
                values: {...this.initialValues, ...initialValues},
                submitError: false,
                filterType: false,
            })
        }
    }

    handleChange = (field, index = null) => event => {
        const { values, errors } = this.state;
        let update = {};

        switch (event.target.type) {
            case 'checkbox':
                update = event.target.checked
                    ? { [field]: [...values[field], index] }
                    : { [field]: filter(values[field], val => val !== index) };
                break;
            default:
                update = index !== null
                    ? { [field]: {...values[field], [index]: event.target.value }}
                    : { [field]: event.target.value };
                break;
        }

        index !== null ? (errors[field] && delete(errors[field][index])) : delete(errors[field]);
        isEmpty(errors[field]) && delete(errors[field]);

        this.setState({
            values: {
                ...values,
                ...update
            },
            errors,
            submitError: false,
        });
    };

    handleCancel = () => {
        this.props.onClose();
        this.setState({values: {...this.initialValues}, errors: {}});
    };

    handleSubmit = event => {
        event.preventDefault();
        const { values } = this.state;

        this.setState({
            submitting: true,
            submitError: false,
        });

        /**
         * Create or update entity
         */
        api.post(`room/convert`, values)
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit);
    };

    handleSuccessSubmit = () => {
        this.props.onSuccess();
        this.props.onClose();
        this.setState({
            values: {...this.initialValues},
            submitting: false
        });
    };

    handleErrorSubmit = (err) => {
        this.setState({submitting: false, submitError: err.message});
    };

    shouldComponentUpdate(nextProps, nextState, nextContext) {
        return nextProps.open || this.props.open;
    }

    handleChangeRoomTypeFilter = event => {
        const { values } = this.state;
        const filterType = event.target.value;

        this.setState({
            values: {
                ...values,
                rooms: [],
            },
            filterType,
        });
    };

    render() {
        const { initialValues, open, empty_rooms, room_types, isFetching } = this.props;
        const {
            values, errors, submitting, submitError,
            filterType,
        } = this.state;

        return (
            <Dialog
                open={open}
                fullWidth={true}
                maxWidth={"sm"}
            >
                {(submitting || isFetching) && <LinearProgress/>}
                <DialogTitle>Смена типа комнат</DialogTitle>
                {!isFetching && <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"change_rooms_types-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={`Конвертировать из типа`}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChangeRoomTypeFilter}
                                    value={filterType}
                                    select={true}
                                >
                                    {map(room_types, rt =>
                                        <MenuItem key={rt.id} value={rt.id}>{rt.title}</MenuItem>
                                    )}
                                </TextField>

                                <Grid container spacing={16}>
                                    <Grid item xs={12}
                                        style={{
                                            overflowY: 'auto',
                                            maxHeight: 350,
                                        }}
                                    >
                                        <FormGroup>
                                            {map(filterType ? filter(empty_rooms, er => er.type === filterType) : {}, r =>
                                                <FormControlLabel
                                                    key={r.id}
                                                    control={
                                                        <Checkbox
                                                            onChange={this.handleChange('rooms', r.id)}
                                                            icon={<CheckBoxOutlineBlankIcon fontSize="small"/>}
                                                            checkedIcon={<CheckBoxIcon fontSize="small"/>}
                                                            checked={values.rooms && values.rooms.includes(r.id)}
                                                            value={`checked`}
                                                        />
                                                    }
                                                    label={`#${r.apartment_num} ${find(room_types, {id: r.type}).title}`}
                                                />
                                            )}
                                        </FormGroup>
                                    </Grid>
                                </Grid>
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={`Конвертировать в тип`}
                                    value={values.room_type}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChange('room_type')}
                                    error={errors.room_type}
                                    helperText={errors.room_type}
                                    select={true}
                                >
                                    {map(room_types, rt =>
                                        <MenuItem key={rt.id} value={rt.id}>{rt.title}</MenuItem>
                                    )}
                                </TextField>
                            </Grid>
                            {values.rooms && values.room_type !== 0 && values.rooms.length > 0 &&
                            <Grid item xs={12}>
                                <br/>
                                <Divider light/>
                                <br/>
                                <Typography variant={`subtitle1`}>Сводка</Typography>
                                <Typography variant={`body2`}>
                                    Будет конвертировано <b>{values.rooms.length}</b> комнат
                                    в тип <b>{find(room_types, {id: values.room_type}).title}</b>
                                </Typography>
                            </Grid>
                            }
                        </Grid>
                    </form>
                    {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
                </DialogContent>}
                <DialogActions>
                    <Button
                        color={"primary"}
                        disabled={submitting || isFetching}
                        onClick={this.handleCancel}
                    >
                        Отмена
                    </Button>
                    <Button
                        variant={"contained"}
                        color={"primary"}
                        form={"change_rooms_types-form"}
                        type={"submit"}
                        disabled={submitting || isFetching}
                    >
                        Конвертировать
                    </Button>
                </DialogActions>
            </Dialog>
        );
    }
}

ChangeRoomsTypesForm.propTypes = {
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

ChangeRoomsTypesForm.defaultProps = {
    initialValues: {

    },
};

const mapStateToProps = state =>
    ({
        empty_rooms: filter(state.abode.room.items, i => isEmpty(i.places)),
        room_types: state.abode.room_type.items,
        isFetching: some([
            state.abode.room,
            state.abode.room_type,
        ], {isFetching: true})
    });

export default connect(mapStateToProps)(ChangeRoomsTypesForm);