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
    Typography,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';
import isEmpty from 'lodash/isEmpty';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";

const api = new API();

class InviteForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                fio: '',
                email: '',
                name: '',
                inn: '',
                kpp: '',
                mngr_first_name: '',
                mngr_last_name: '',
                mngr_email: '',
                mngr_phone: '',
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
        const { name, value } = event.target;

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

    handleSubmit = event => {
        event.preventDefault();
        const { values } = this.state;

        this.setState({
            submitting: true,
            submitError: false,
        });

        api.post(`conference_organization/invite`, values)
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
                <DialogTitle>Отправка приглашения</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"invite-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    label={"Обращение"}
                                    value={values.fio}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'fio'}
                                    onChange={this.handleChange('fio')}
                                    error={!!errors.fio}
                                    helperText={errors.fio}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    label={"Email"}
                                    value={values.email}
                                    type={`email`}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'email'}
                                    onChange={this.handleChange('email')}
                                    error={!!errors.email}
                                    helperText={errors.email}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    label={"Наименование организации"}
                                    value={values.name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'name'}
                                    onChange={this.handleChange('name')}
                                    error={!!errors.name}
                                    helperText={errors.name}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"ИНН"}
                                    type={"number"}
                                    value={values.inn}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'inn'}
                                    onChange={this.handleChange('inn')}
                                    error={!!errors.inn}
                                    helperText={errors.inn}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    // required
                                    label={"КПП"}
                                    type={"number"}
                                    value={values.kpp}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'kpp'}
                                    onChange={this.handleChange('kpp')}
                                    error={!!errors.kpp}
                                    helperText={errors.kpp}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Typography>Информация о менеджере</Typography>
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Фамилия"}
                                    value={values.mngr_last_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'mngr_last_name'}
                                    onChange={this.handleChange('mngr_last_name')}
                                    error={!!errors.mngr_last_name}
                                    helperText={errors.mngr_last_name}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Имя"}
                                    value={values.mngr_first_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'mngr_first_name'}
                                    onChange={this.handleChange('mngr_first_name')}
                                    error={!!errors.mngr_first_name}
                                    helperText={errors.mngr_first_name}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Email"}
                                    value={values.mngr_email}
                                    type={`email`}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'mngr_email'}
                                    onChange={this.handleChange('mngr_email')}
                                    error={!!errors.mngr_email}
                                    helperText={errors.mngr_email}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Телефон"}
                                    value={values.mngr_phone}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'mngr_phone'}
                                    onChange={this.handleChange('mngr_phone')}
                                    error={!!errors.mngr_phone}
                                    helperText={errors.mngr_phone}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                        </Grid>
                    </form>
                    {submitError && <ErrorMessage description={submitError} extended={true}/>}
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
                        form={"invite-form"}
                        type={"submit"}
                        disabled={submitting}
                    >
                        Отправить
                    </Button>
                </DialogActions>
            </Dialog>
        );
    }
}

InviteForm.propTypes = {
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

export default InviteForm;