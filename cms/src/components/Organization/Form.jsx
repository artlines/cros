import React from "react";
import PropTypes from "prop-types";
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    FormControl,
    FormControlLabel,
    FormHelperText,
    Switch,
    TextField,
    LinearProgress,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';
import isEmpty from 'lodash/isEmpty';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";
import ConfirmDialog from "../utils/ConfirmDialog";

const api = new API();

class OrganizationForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                name: '',
                hidden: false,
                inn: '',
                kpp: '',
                city: '',
                address: '',
                requisites: '',
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

        let value;
        switch (event.target.type) {
            case 'checkbox':
                value = event.target.checked;
                break;
            default:
                value = event.target.value;
                break;
        }

        const update = index !== null
            ? { [field]: {...values[field], [index]: value }}
            : { [field]: value };

        index !== null
            ? (errors[field] && delete(errors[field][index]))
            : delete(errors[field]);

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
        const { initialValues } = this.props;

        this.setState({
            submitting: true,
            submitError: false,
        });

        /** Create or update entity */
        const id = initialValues && initialValues.id;
        if (!id) {
            api.post(`conference_organization/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        } else {
            api.put(`conference_organization/${id}`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        }
    };

    handleDelete = (id) => {
        api.delete(`conference_organization/${id}`)
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
                <DialogTitle>{isUpdate ? 'Редактирование' : 'Добавление'} организации</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"organization-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    label={"Наименование"}
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
                            <Grid item xs={12} sm={12}>
                                <FormControl>
                                    <FormControlLabel
                                        label={"Скрыть организацию в списке участников на сайте"}
                                        control={
                                            <Switch
                                                checked={values.hidden}
                                                onChange={this.handleChange('hidden')}
                                            />
                                        }
                                    />
                                    <FormHelperText>Для возможности управления данными организации</FormHelperText>
                                </FormControl>
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
                                    required
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
                                <TextField
                                    required
                                    label={"Город"}
                                    value={values.city}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'city'}
                                    onChange={this.handleChange('city')}
                                    error={!!errors.city}
                                    helperText={errors.city}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    label={"Адрес"}
                                    value={values.address || ''}
                                    margin={"dense"}
                                    fullWidth
                                    multiline
                                    rows={4}
                                    variant={"outlined"}
                                    name={'address'}
                                    onChange={this.handleChange('address')}
                                    error={!!errors.address}
                                    helperText={errors.address}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    label={"Реквизиты"}
                                    value={values.requisites || ''}
                                    margin={"dense"}
                                    fullWidth
                                    multiline
                                    rows={6}
                                    variant={"outlined"}
                                    name={'requisites'}
                                    onChange={this.handleChange('requisites')}
                                    error={!!errors.requisites}
                                    helperText={errors.requisites}
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
                                        form={"organization-form"}
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

OrganizationForm.propTypes = {
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

export default OrganizationForm;