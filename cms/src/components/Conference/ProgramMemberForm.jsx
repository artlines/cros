import React from 'react';
import {connect} from 'react-redux';
import {
    Button,
    FormControl,
    FormControlLabel,
    FormHelperText,
    Grid,
    Switch,
    TextField,
    Typography,
} from '@material-ui/core';
import { green, red } from '@material-ui/core/colors';
import { Formik } from 'formik';
import WysiwygField from '../../components/utils/WysiwygField';
import SuggestingSelectField from '../../components/utils/SuggestingSelectField';
import noPhotoImg from '../../theme/images/no_photo.jpg';
import map from "lodash/map";

class ProgramMemberForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {},
            photo: {
                src: noPhotoImg,
                width: 295,
                height: 350,
                recommendWidth: 295,
                recommendHeight: 350,
            },
        };

        this.photoRef = React.createRef();
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { photo } = this.state;
        const photoRef = this.photoRef.current;

        /**
         * Check that photo was updated and update its parameters
         * @bug photoRef gives previous photo
         * TODO
         */
        if (prevState.photo.src !== photo.src) {
            this.setState({ photo: { ...photo, width: photoRef.naturalWidth, height: photoRef.naturalHeight } });
        }
    }

    handlePhotoAttach = event => {
        if (event.target.files && event.target.files[0]) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = this.updatePhotoInfo;
            reader.readAsDataURL(file);
        }
    };

    updatePhotoInfo = event => {
        this.setState({
            photo: {
                ...this.state.photo,
                src: event.target.result,
            },
        });
    };

    render() {
        const { members } = this.props;
        const { values, photo } = this.state;

        return (
            <React.Fragment>
                <Grid container justify={`center`} style={{marginTop: '24px'}}>
                    <Grid item>
                        <Grid container>
                            <Grid item xs={12} md={6} style={{textAlign: 'center'}}>
                                <Grid container spacing={16}>
                                    <Grid item xs={12}>
                                        <img ref={this.photoRef} src={photo.src} style={{width: '100%', maxWidth: '295px'}}/>
                                    </Grid>
                                    <Grid item xs={12}>
                                        {/*<Typography variant={`caption`}>*/}
                                            {/*Размер текущего изображения:&nbsp;*/}
                                            {/*<span style={{*/}
                                                {/*color: (photo.width === photo.recommendWidth && photo.height === photo.recommendHeight)*/}
                                                    {/*? green[700]*/}
                                                    {/*: red[700],*/}
                                            {/*}}>*/}
                                                {/*{photo.width} x {photo.height}px*/}
                                            {/*</span>*/}
                                        {/*</Typography>*/}
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
                                            // onChange={this.handleFilterChange(`invited_by[]`)}
                                            isSearchable
                                            placeholder={`Начните вводить имя`}
                                            label={`Участник`}
                                            fullWidth
                                        />
                                    </Grid>
                                    <Grid item xs={12}>
                                        <WysiwygField
                                            label={`Описание`}
                                            name={`description`}
                                            onChange={() => {}}
                                        />
                                    </Grid>
                                    <Grid item xs={9}>
                                        <FormControl>
                                            <FormControlLabel
                                                label={"Отображать на сайте"}
                                                control={
                                                    <Switch
                                                        checked={true}
                                                        // onChange={this.handleChange('representative')}
                                                    />
                                                }
                                            />
                                            {/*<FormHelperText>Для возможности управления данными организации</FormHelperText>*/}
                                        </FormControl>
                                    </Grid>
                                    <Grid item xs={3}>
                                        <TextField
                                            fullWidth
                                            label={`Сортировка`}
                                            type={`number`}
                                        />
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

const mapStateToProps = state =>
    ({
        members: state.participating.member.items,
    });

export default connect(mapStateToProps)(ProgramMemberForm);