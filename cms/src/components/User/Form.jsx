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
    LinearProgress,
    MenuItem, FormControl, FormControlLabel, Switch, FormHelperText, InputLabel,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';
import isEmpty from 'lodash/isEmpty';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";
import ConfirmDialog from "../utils/ConfirmDialog";
import map from 'lodash/map';
import SuggestingSelectField from "../utils/SuggestingSelectField";

const api = new API();

class UserForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                first_name:         '',
                last_name:          '',
                middle_name:        '',
                sex:                1,
                email:              '',
                phone:              '',
                organization_id:    0,
                post:               '',
                representative:     false,
                is_active:          true,
                role:               'ROLE_USER',
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
        if (field === 'organization_id') {
            value = event.value;
        } else {
            switch (event.target.type) {
                case 'checkbox':
                    value = event.target.checked;
                    break;
                default:
                    value = event.target.value;
                    break;
            }
        }

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
        const { initialValues } = this.props;

        this.setState({
            submitting: true,
            submitError: false,
        });

        /** Create or update entity */
        const id = initialValues && initialValues.id;
        if (!id) {
            api.post(`users/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        } else {
            api.put(`users/${id}`, values)
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
        const { initialValues, open, roles, organizations } = this.props;
        const { values, errors, submitting, submitError } = this.state;

        const isUpdate = initialValues && initialValues.id;

        return (
            <Dialog
                open={open}
                fullWidth={true}
                maxWidth={"sm"}
            >
                {submitting && <LinearProgress/>}
                <DialogTitle>{isUpdate ? 'Редактирование' : 'Добавление'} пользователя</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"user-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Имя"}
                                    value={values.first_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'first_name'}
                                    onChange={this.handleChange('first_name')}
                                    error={!!errors.first_name}
                                    helperText={errors.first_name}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Фамилия"}
                                    value={values.last_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'last_name'}
                                    onChange={this.handleChange('last_name')}
                                    error={!!errors.last_name}
                                    helperText={errors.last_name}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    label={"Отчество"}
                                    value={values.middle_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'middle_name'}
                                    onChange={this.handleChange('middle_name')}
                                    error={!!errors.middle_name}
                                    helperText={errors.middle_name}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Пол"}
                                    value={values.sex}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'sex'}
                                    onChange={this.handleChange('sex')}
                                    error={!!errors.sex}
                                    helperText={errors.sex}
                                    select={true}
                                    InputLabelProps={{shrink: true}}
                                >
                                    <MenuItem value={1}>Мужской</MenuItem>
                                    <MenuItem value={2}>Женский</MenuItem>
                                </TextField>
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Email"}
                                    value={values.email}
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
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Телефон"}
                                    value={values.phone}
                                    margin={"dense"}
                                    type={`number`}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'phone'}
                                    onChange={this.handleChange('phone')}
                                    error={!!errors.phone}
                                    helperText={errors.phone}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <SuggestingSelectField
                                    options={map(organizations, i => ({ value: i.id, label: i.name }))}
                                    onChange={this.handleChange(`organization_id`)}
                                    isSearchable
                                    placeholder={`Начните вводить имя`}
                                    value={[values.organization_id]}
                                    required
                                    label={"Организация"}
                                    fullWidth
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    label={"Должность"}
                                    value={values.post}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'post'}
                                    onChange={this.handleChange('post')}
                                    error={!!errors.post}
                                    helperText={errors.post}
                                    InputLabelProps={{shrink: true}}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <FormControl>
                                    <FormControlLabel
                                        label={"Представитель организации"}
                                        control={
                                            <Switch
                                                checked={values.representative}
                                                onChange={this.handleChange('representative')}
                                            />
                                        }
                                    />
                                    <FormHelperText>Для возможности управления данными организации</FormHelperText>
                                </FormControl>
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <FormControl>
                                    <FormControlLabel
                                        label={"Активен?"}
                                        control={
                                            <Switch
                                                checked={values.is_active}
                                                onChange={this.handleChange('is_active')}
                                            />
                                        }
                                    />
                                    <FormHelperText>Включение/Отключение пользователя</FormHelperText>
                                </FormControl>
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Роль в системе"}
                                    value={values.role}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'role'}
                                    onChange={this.handleChange('role')}
                                    error={!!errors.role}
                                    helperText={errors.role}
                                    InputLabelProps={{shrink: true}}
                                    select={true}
                                >
                                    {map(roles, role =>
                                        <MenuItem key={role.key} value={role.key}>{role.title}</MenuItem>
                                    )}
                                </TextField>
                            </Grid>
                        </Grid>
                    </form>
                    {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
                </DialogContent>
                <DialogActions>
                    <Grid container spacing={0} justify={`space-between`}>
                        <Grid item>
                            {false && isUpdate &&
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
                                        form={"user-form"}
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

UserForm.propTypes = {
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
        roles: state.system.roles.items,
        organizations: state.participating.organization_directory.items,
    });

export default connect(mapStateToProps)(UserForm);