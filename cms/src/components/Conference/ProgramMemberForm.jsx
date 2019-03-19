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
} from '@material-ui/core';
import { Formik } from 'formik';
import WysiwygField from '../../components/utils/WysiwygField';
import SuggestingSelectField from '../../components/utils/SuggestingSelectField';
import noPhotoImg from '../../theme/images/no_photo.jpg';
import map from "lodash/map";

class ProgramMemberForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                photo: noPhotoImg,
            },
        };
    }

    handlePhotoAttach = event => {
        console.log(event.target.files);
    };

    render() {
        const { members } = this.props;
        const { values } = this.state;

        return (
            <React.Fragment>
                <Grid container justify={`center`} style={{marginTop: '24px'}}>
                    <Grid item>
                        <Grid container>
                            <Grid item xs={12} md={6} style={{textAlign: 'center'}}>
                                <Grid container>
                                    <Grid item xs={12}>
                                        <img src={values.photo} style={{width: '100%', maxWidth: '350px'}}/>
                                    </Grid>
                                    <Grid item xs={12}>
                                        <input
                                            id={`photo`}
                                            style={{display: 'none'}}
                                            type={`file`}
                                            onChange={this.handlePhotoAttach}
                                        />
                                        <label htmlFor={`photo`}>
                                            <Button>Загрузить</Button>
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
                                    <Grid item xs={12}>
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
                                    <Grid item xs={6}>
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