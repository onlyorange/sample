using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCDto.DTO;
namespace PHCRepos.Repos.AbstractContracts
{
    public abstract class IFbprofileDbProc
    {

        public abstract  int usercount(FbDTO dto);

        public abstract FbDTO  Save_user(FbDTO dto);

        public abstract  FbDTO update_user(FbDTO dto);

        public abstract PetProfileDTO SavePetDetSignUp(PetProfileDTO dto);


        public abstract int UpdateUID(PetProfileDTO dt);

        public abstract FbDTO GetUserByFacebookId(string fbId);

        public abstract void UserUpdateForShare(FbDTO user);
    }
}
