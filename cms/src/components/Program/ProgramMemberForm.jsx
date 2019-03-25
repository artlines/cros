import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {
    Button,
    FormControl,
    FormControlLabel,
    Grid,
    Switch,
    TextField,
    Typography,
} from '@material-ui/core';
import { Formik } from 'formik';
import WysiwygField from '../utils/WysiwygField';
import ErrorMessage from '../utils/ErrorMessage';
import ConfirmDialog from '../utils/ConfirmDialog';
import SuggestingSelectField from '../utils/SuggestingSelectField';
import noPhotoImg from '../../theme/images/no_photo.jpg';
import map from "lodash/map";
import isEqual from "lodash/isEqual";
import isEmpty from "lodash/isEmpty";
import API from "../../libs/api";

const api = new API();

class ProgramMemberForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {

            values: {
                conference_member_id: '',
                description: '',
                publish: true,
                ordering: 100,
                photo_original: noPhotoImg,
            },
            errors: {},
            submitting: false,
            submitError: false,

            photo: {
                width: 295,
                height: 350,
                recommendWidth: 295,
                recommendHeight: 350,
            },
        };
    }

    componentDidMount() {
        const { initialValues } = this.props;
        const { values } = this.state;
        !!initialValues && this.setState({
            values: {...values, ...initialValues},
        });
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { initialValues } = this.props;
        const { values } = this.state;

        /** Check for updates initialValues */
        if (!isEqual(prevProps.initialValues, initialValues)) {
            this.setState({
                values: {...values, ...initialValues},
                submitError: false,
            })
        }
    }

    handleChange = (field, index = null) => event => {
        const {values, errors} = this.state;

        let value;
        if (event.editor) {
            value = event.editor.getData();
        } else if (field === 'conference_member_id') {
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
            api.post(`program_member/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        } else {
            api.put(`program_member/${id}`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        }
    };

    handleDelete = id => {
        api.delete(`program_member/${id}`)
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

    handlePhotoAttach = event => {
        if (event.target.files && event.target.files[0]) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = event => {
                this.setState({
                    values: {
                        ...this.state.values,
                        photo_original: event.target.result,
                    },
                })
            };
            reader.readAsDataURL(file);
        }
    };

    render() {
        const { initialValues, members } = this.props;
        const { values, errors, submitting, submitError, photo } = this.state;

        const isUpdate = initialValues.id || false;

        return (
            <React.Fragment>
                <Grid container justify={`center`} style={{marginTop: '24px'}}>
                    <Grid item>
                        <Grid container>
                            <Grid item xs={12} md={6} style={{textAlign: 'center'}}>
                                <Grid container spacing={16}>
                                    <Grid item xs={12}>
                                        <img src={values.photo_original} style={{width: '100%', maxWidth: '295px'}}/>
                                    </Grid>
                                    <Grid item xs={12}>
                                        <Typography variant={`caption`}>
                                            Рекомендуемый размер изображения:&nbsp;
                                            <span>{photo.recommendWidth} x {photo.recommendHeight}px</span>
                                        </Typography>
                                    </Grid>
                                    <Grid item xs={12}>
                                        <input
                                            accept={`image/*`}
                                            id={`photo_file_upload`}
                                            style={{display: 'none'}}
                                            type={`file`}
                                            onChange={this.handlePhotoAttach}
                                        />
                                        <label htmlFor={`photo_file_upload`}>
                                            <Button
                                                component={`span`}
                                                variant={`contained`}
                                                disableRipple
                                            >Загрузить</Button>
                                        </label>
                                    </Grid>
                                </Grid>
                            </Grid>
                            <Grid item xs={12} md={6} style={{textAlign: 'center'}}>
                                <Grid container spacing={16}>
                                    <Grid item xs={12}>
                                        <SuggestingSelectField
                                            options={map(members, i => ({ value: i.id, label: `${i.first_name} ${i.last_name}` }))}
                                            onChange={this.handleChange(`conference_member_id`)}
                                            isSearchable
                                            placeholder={`Начните вводить имя`}
                                            label={`Участник`}
                                            fullWidth
                                            value={[values.conference_member_id]}
                                            disabled={isUpdate}
                                        />
                                    </Grid>
                                    <Grid item xs={4}>
                                        <TextField
                                            label={`Сортировка`}
                                            type={`number`}
                                            value={values.ordering}
                                            onChange={this.handleChange(`ordering`)}
                                        />
                                    </Grid>
                                    <Grid item xs={8}>
                                        <FormControl>
                                            <FormControlLabel
                                                label={"Отображать на сайте"}
                                                control={
                                                    <Switch
                                                        checked={values.publish}
                                                        onChange={this.handleChange(`publish`)}
                                                    />
                                                }
                                            />
                                            {/*<FormHelperText>Для возможности управления данными организации</FormHelperText>*/}
                                        </FormControl>
                                    </Grid>
                                    <Grid item xs={12}>
                                        <WysiwygField
                                            label={`Описание`}
                                            name={`description`}
                                            onChange={this.handleChange(`description`)}
                                            value={values.description}
                                        />
                                    </Grid>
                                </Grid>
                            </Grid>
                            <Grid item xs={12}>
                                {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
                            </Grid>
                            <Grid item xs={12}>
                                <Grid container spacing={16} justify={`center`} direction={`row`}>
                                    <Grid item>
                                        {isUpdate &&
                                        <ConfirmDialog
                                            onConfirm={() => this.handleDelete(isUpdate)}
                                            trigger={
                                                <Button
                                                    disabled={submitting}
                                                    variant="contained"
                                                    color="red"
                                                    size={`large`}
                                                >Удалить</Button>
                                            }
                                        />
                                        }

                                    </Grid>
                                    <Grid item>
                                        <Button
                                            disabled={submitting}
                                            variant="contained"
                                            size={`large`}
                                            onClick={this.handleCancel}
                                        >Отмена</Button>
                                    </Grid>
                                    <Grid item>
                                        <Button
                                            disabled={submitError || submitting}
                                            variant="contained"
                                            color="primary"
                                            size={`large`}
                                            onClick={this.handleSubmit}
                                        >Сохранить</Button>
                                    </Grid>
                                </Grid>
                            </Grid>
                        </Grid>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

ProgramMemberForm.propTypes = {
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

ProgramMemberForm.defaultProps = {
    initialValues: {},
};

const mapStateToProps = state =>
    ({
        members: state.participating.member.items,
    });

export default connect(mapStateToProps)(ProgramMemberForm);