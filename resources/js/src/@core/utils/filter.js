import { isToday } from './utils'
import moment from 'moment'

moment.locale('id')

export const kFormatter = num => (num > 999 ? `${(num / 1000).toFixed(1)}k` : num)

export const title = (value, replacer = ' ') => {
  if (!value) return ''
  const str = value.toString()

  const arr = str.split(replacer)
  const capitalizedArray = []
  arr.forEach(word => {
    const capitalized = word.charAt(0).toUpperCase() + word.slice(1)
    capitalizedArray.push(capitalized)
  })
  return capitalizedArray.join(' ')
}

export const formatDateTime = value => {
  if (value) {
     return moment(String(value)).format('LLL')
  }
}

export const isToText = value => {
    if (value) {
        return 'Yes';
    }

    return 'No';
}

export const formatDate = value => {
  if (value) {
     return moment(String(value)).format('DD MMMM YYYY')
  }
}

export const formatTime = value => {
    if (value) {
        const time = moment(String(value), 'HH:mm:ss')
        if (time.isValid()) {
            return time.format('HH:mm');
        }
    }
}

export const formatDateShort = value => {
  if (value) {
     return moment(String(value)).format('DD/MM/YYYY')
  }
}

export const yearsLastAndStart = value => {
    var array = []
    for (let i = 1; i <= value; i++) {
        array.push({'name': moment().add(i, 'years').year()});
    }
    for (let i = 0; i <= value; i++) {
        array.push({'name' : moment().subtract(i, 'years').year()});
    }
    return array;
}

export const formatDateTimeShort = value => {
  if (value) {
     return moment(String(value)).format('DD/MM/YYYY H:m')
  }
}

export const formatDateBetween = (start, end) => {
  if (start && end) {
     return moment(String(start)).format('DD MMMM') +" - "+ moment(String(end)).format('DD MMMM YYYY')
  }
}

export const formatDateToMonth = value => {
  if (value) {
     return moment(String(value)).format('MMMM YYYY')
  }
}

export const formatGetMonth = value => {
  if (value) {
     return moment(String(value)).format('MMMM')
  }
}

export const formatGetYear = value => {
  if (value) {
     return moment(String(value)).format('YYYY')
  }
}

export const avatarText = value => {
  if (!value) return ''
  const nameArray = value.split(' ')
  return nameArray.map(word => word.charAt(0).toUpperCase()).join('').substring(0, 3)
}

/**
 * Return short human friendly month representation of date
 * Can also convert date to only time if date is of today (Better UX)
 * @param {String} value date to format
 * @param {Boolean} toTimeForCurrentDay Shall convert to time if day is today/current
 */
export const formatDateToMonthShort = (value, toTimeForCurrentDay = true) => {
  const date = new Date(value)
  let formatting = { month: 'short', day: 'numeric' }

  if (toTimeForCurrentDay && isToday(date)) {
    formatting = { hour: 'numeric', minute: 'numeric' }
  }

  return new Intl.DateTimeFormat('en-US', formatting).format(new Date(value))
}

// Strip all the tags from markup and return plain text
export const filterTags = value => value.replace(/<\/?[^>]+(>|$)/g, '')

export const capitalize = value => {
  var strVal = ''
  value = value.toLowerCase()
  value = value.split(' ')
  for (var chr = 0; chr < value.length; chr++) {
    strVal += value[chr].substring(0, 1).toUpperCase() + value[chr].substring(1, value[chr].length) + ' '
  }
  return strVal
}

export const calculateAge = value => {
  var ageDifMs = Date.now() - new Date(value).getTime()
  var ageDate = new Date(ageDifMs) // miliseconds from epoch
  return Math.abs(ageDate.getUTCFullYear() - 1970)
}

export const roundDigit = (value, near) => {
  if(value%near===0) return value
    return near * Math.round(value / near);
}

export const hasDifferentValues = (arr, key) => {
  const uniqueValues = new Set(arr.map(item => item[key]));
  return uniqueValues.size > 1; // True if there is more than one unique value
}

