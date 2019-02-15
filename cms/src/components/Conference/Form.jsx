import React from "react";
import PropTypes from "prop-types";
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    TextField,
    LinearProgress,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';
import isEmpty from 'lodash/isEmpty';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";
import ConfirmDialog from "../utils/ConfirmDialog";

const api = new API();

class ConferenceForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                year: '',
                users_limit_global: '',
                users_limit_by_org: '',
                reg_start: '',
                reg_finish: '',
                event_start: '',
                event_finish: '',
            },
            errors: {},
            submitting: false,
            submitError: false,
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
            this.setState({
                values: {...values, ...initialValues},
                submitError: false,
            })
        }
    }

    shouldComponentUpdate(nextProps, nextState, nextContext) {
        return nextProps.open || this.props.open;
    }

    handleChange = (field, index = null) => event => {
        const { values, errors } = this.state;

        const value = event.target.value;

        const update = index !== null ? { [field]: {...values[field], [index]: value }} : { [field]: value };

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
        this.setState({values: {}, errors: {}});
    };

    handleDelete = (id) => {
        api.delete(`conference/${id}`)
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit);
    };

    handleSubmit = event => {
        event.preventDefault();
        const { values } = this.state;
        const { initialValues } = this.props;

        this.setState({
            submitting: true,
            submitError: false,
        });

        /** Create or update entity */
        const id = initialValues && initialValues.id;
        if (!id) {
            api.post(`conference/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        } else {
            api.put(`conference/${id}`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        }
    };

    handleSuccessSubmit = () => {
        this.props.onSuccess();
        this.props.onClose();
        this.setState({
            values: {...this.initialValues},
            submitting: false
        });
    };

    handleErrorSubmit = (err) => this.setState({submitting: false, submitError: err.message});

    render() {
        const { initialValues, open } = this.props;
        const { values, errors, submitting, submitError } = this.state;

        const isUpdate = initialValues && initialValues.id;

        return (
            <Dialog
                open={open}
                fullWidth={true}
                maxWidth={"sm"}
            >
                {submitting && <LinearProgress/>}
                <DialogTitle>{isUpdate ? 'Редактирование' : 'Добавление'} конференции</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"conference-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Год"}
                                    value={values.year}
                                    margin={"dense"}
                                    type={`number`}
                                    inputProps={{ min: 2000, max: 2100, step: 1 }}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'year'}
                                    onChange={this.handleChange('year')}
                                    error={!!errors.year}
                                    helperText={errors.year}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Лимит участников на конферецию"}
                                    value={values.users_limit_global}
                                    margin={"dense"}
                                    type={`number`}
                                    inputProps={{ min: 0, step: 1 }}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'users_limit_global'}
                                    onChange={this.handleChange('users_limit_global')}
                                    error={!!errors.users_limit_global}
                                    helperText={errors.users_limit_global}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Лимит участников на организацию"}
                                    value={values.users_limit_by_org}
                                    margin={"dense"}
                                    type={`number`}
                                    inputProps={{ min: 0, step: 1 }}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'users_limit_by_org'}
                                    onChange={this.handleChange('users_limit_by_org')}
                                    error={!!errors.users_limit_by_org}
                                    helperText={errors.users_limit_by_org}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Дата начала регистрации"}
                                    type={"datetime-local"}
                                    value={values.reg_start}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'reg_start'}
                                    onChange={this.handleChange('reg_start')}
                                    error={!!errors.reg_start}
                                    helperText={errors.reg_start}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Дата окончания регистрации"}
                                    type={"datetime-local"}
                                    value={values.reg_finish}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'reg_finish'}
                                    onChange={this.handleChange('reg_finish')}
                                    error={!!errors.reg_finish}
                                    helperText={errors.reg_finish}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Дата начала мероприятия"}
                                    type={"datetime-local"}
                                    value={values.event_start}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'event_start'}
                                    onChange={this.handleChange('event_start')}
                                    error={!!errors.event_start}
                                    helperText={errors.event_start}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Дата окончания мероприятия"}
                                    type={"datetime-local"}
                                    value={values.event_finish}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'event_finish'}
                                    onChange={this.handleChange('event_finish')}
                                    error={!!errors.event_finish}
                                    helperText={errors.event_finish}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                        </Grid>
                    </form>
                    {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
                </DialogContent>
                <DialogActions>
                    <Grid container spacing={0} justify={`space-between`}>
                        <Grid item>
                            {isUpdate &&
                            <ConfirmDialog
                                onConfirm={() => this.handleDelete(isUpdate)}
                                trigger={<Button
                                    color={"secondary"}
                                    disabled={submitting}
                                >
                                    Удалить
                                </Button>}
                            />
                            }
                        </Grid>
                        <Grid item>
                            <Grid container spacing={8}>
                                <Grid item>
                                    <Button
                                        color={"primary"}
                                        disabled={submitting}
                                        onClick={this.handleCancel}
                                    >
                                        Отмена
                                    </Button>
                                </Grid>
                                <Grid item>
                                    <Button
                                        variant={"contained"}
                                        color={"primary"}
                                        form={"conference-form"}
                                        type={"submit"}
                                        disabled={submitting}
                                    >
                                        {isUpdate ? 'Редактировать' : 'Добавить'}
                                    </Button>
                                </Grid>
                            </Grid>
                        </Grid>
                    </Grid>
                </DialogActions>
            </Dialog>
        );
    }
}

ConferenceForm.propTypes = {
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

export default ConferenceForm;