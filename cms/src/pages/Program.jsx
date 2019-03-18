import React from 'react';
import {
    Collapse,
    Paper,
    Tab,
    Tabs,
} from '@material-ui/core';
import {
    RecordVoiceOver,
    PersonAdd,
} from '@material-ui/icons';
import SpeedDialMenu from '../components/utils/SpeedDialMenu';

const actions = [
    {
        icon: <RecordVoiceOver/>,
        title: 'Добавить спикера',
        tooltipOpen: true,
        onClick: () => console.log(`Click Add Speaker`),
    },
    {
        icon: <PersonAdd/>,
        title: 'Добавить члена комитета',
        tooltipOpen: true,
        onClick: () => console.log(`Click Add Program Committee Member`),
    },
];

function Program() {
    const [currentTab, setTab] = React.useState(0);
    const [showForm, setShowForm] = React.useState(false);

    function handleChangeTab(event, tab) {
        setTab(tab);
    }

    return (
        <React.Fragment>
            <Paper>
                <Tabs
                    value={currentTab}
                    variant={`fullWidth`}
                    onChange={handleChangeTab}
                    indicatorColor="primary"
                    textColor="primary"
                >
                    <Tab label={`Комитет`}/>
                    <Tab label={`Спикеры`}/>
                    <Tab disabled label={`Расписание`}/>
                </Tabs>
            </Paper>
            <Collapse in={open} unmountOnExit={true}>

            </Collapse>
            <SpeedDialMenu actions={actions}/>
        </React.Fragment>
    );
}

export default Program;