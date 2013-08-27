using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PHCDto.DTO
{
    public class LoginDTO
    {
        [Required(ErrorMessage = "Required")]
        [RegularExpression(".+\\@.+\\..+", ErrorMessage = "Not a valid")]
        public string uname { get; set; }
        [Required(ErrorMessage = "Required")]
        public string pwd { get; set; }
    }
}
