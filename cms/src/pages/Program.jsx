import React from 'react';
import {
    AppBar,
    Tab,
    Tabs,
} from '@material-ui/core';

function Program() {
    const [value, setValue] = React.useState(0);

    function handleChange(event, newValue) {
        setValue(newValue);
    }

    return (
        <AppBar position={`static`}>
            <Tabs value={value} variant={`fullWidth`} onChange={handleChange}>
                <Tab label={`Комитет`}/>
                <Tab label={`Спикеры`}/>
                <Tab label={`Расписание`}/>
            </Tabs>
        </AppBar>
    );
}

export default Program;