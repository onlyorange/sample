using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCRepos.Repos.AbstractContracts;
using PHCRepos.Repos.Core;
using PHCDto.DTO;
using PHCService.Service.Core;
using PHCService.Service.InterfaceContracts;

namespace PHCService.Service.Core
{
    public class FbprofilePres : IFbprofilePres
    {
        private IFbprofileDbProc _ifbpdb;

        public FbprofilePres(IFbprofileDbProc view)
        {
            if (view == null) throw new ArgumentNullException();
            _ifbpdb = view;
        }


        public int userCount(PHCDto.DTO.FbDTO dto)
        {
            return _ifbpdb.usercount(dto);
        }

        public FbDTO Save_user(PHCDto.DTO.FbDTO dto)
        {
           return this. _ifbpdb.Save_user(dto);
        }

        public FbDTO update_user(PHCDto.DTO.FbDTO dto)
        {
            return this._ifbpdb.update_user(dto);
        }

        public void UserUpdateForShare(FbDTO user)
        {
            _ifbpdb.UserUpdateForShare(user);
        }

        public PetProfileDTO SavePetDetSignUp(PetProfileDTO dto)
        {
            return _ifbpdb.SavePetDetSignUp(dto);
        }

        public int UpdateUID(PetProfileDTO dt)
        {
            return _ifbpdb.UpdateUID(dt);
        }

        /// <summary>
        /// Gets a user by FacebookId
        /// </summary>
        /// <param name="fbId"></param>
        /// <returns></returns>
        public FbDTO GetUserByFacebookId(string fbId) {
            return _ifbpdb.GetUserByFacebookId(fbId);
        }

    }
}
