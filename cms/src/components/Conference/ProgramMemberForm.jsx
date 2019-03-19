import React from 'react';
import {connect} from 'react-redux';
import {
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

function ProgramMemberForm({ members, onClose }) {


    return (
        <React.Fragment>
            <Grid container justify={`center`} style={{marginTop: '24px'}}>
                <Grid item>
                    <Grid container spacing={16}>
                        <Grid item xs={12} md={6} style={{textAlign: 'center'}}>
                            <img src={noPhotoImg} style={{maxWidth: '100%'}}/>
                        </Grid>
                        <Grid item xs={12} md={6} style={{textAlign: 'center'}}>
                            <Grid container spacing={16}>
                                <Grid item xs={12}>
                                    <TextField
                                        fullWidth
                                        label={`Участник`}
                                    />
                                    <SuggestingSelectField
                                        options={map(members, i => ({ value: i.id, label: `${i.first_name} ${i.last_name}` }))}
                                        // onChange={this.handleFilterChange(`invited_by[]`)}
                                        isSearchable
                                        isMulti
                                        placeholder={`Начните вводить имя`}
                                        label={`Ответственный менеджер`}
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
                                <Grid item xs={6}>
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

const mapStateToProps = state =>
    ({
        members: state.participating.members,
    });

export default connect(mapStateToProps)(ProgramMemberForm);